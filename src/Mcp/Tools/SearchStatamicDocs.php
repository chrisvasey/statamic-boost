<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use ChrisVasey\StatamicBoost\EnvironmentDetector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class SearchStatamicDocs extends Tool
{
    protected string $description = 'Search the Statamic documentation (version-matched) using semantic search. Use simple, topic-based queries for best results.';

    /**
     * Cached parsed documentation sections.
     *
     * @var array<int, array{title: string, source: string, description: string, content: string}>|null
     */
    protected static ?array $cachedSections = null;

    /**
     * Cache key based on file modification time.
     */
    protected static ?int $cacheKey = null;

    /**
     * Tracks current docs path for cache invalidation.
     */
    protected static ?string $currentDocsPath = null;

    public function schema(JsonSchema $schema): array
    {
        return [
            'queries' => $schema->array(
                $schema->string()
            )
                ->description('One or more search queries (e.g., ["antlers templating", "collections routing", "blueprints fields"])')
                ->required(),
            'limit' => $schema->integer()
                ->description('Maximum results per query (default: 5)')
                ->default(5),
        ];
    }

    public function handle(Request $request): Response
    {
        $queries = $request->get('queries', []);
        $limit = $request->get('limit', 5);

        if (empty($queries)) {
            return Response::error('At least one query is required.');
        }

        $docsPath = $this->getDocsPath();

        if (! file_exists($docsPath)) {
            return Response::error('Documentation file not found. Please ensure the statamic-boost package is installed correctly.');
        }

        $sections = $this->getCachedSections($docsPath);
        $results = [];

        foreach ($queries as $query) {
            $matches = $this->searchSections($sections, $query, $limit);
            $results[$query] = $matches;
        }

        return Response::json([
            'query_count' => count($queries),
            'total_sections' => count($sections),
            'results' => $results,
        ]);
    }

    /**
     * Get cached sections, parsing only when file changes.
     *
     * @return array<int, array{title: string, source: string, description: string, content: string}>
     */
    protected function getCachedSections(string $docsPath): array
    {
        $mtime = filemtime($docsPath);

        // Return cached sections if file hasn't changed and path is the same
        if (static::$cachedSections !== null
            && static::$cacheKey === $mtime
            && static::$currentDocsPath === $docsPath) {
            return static::$cachedSections;
        }

        // Parse and cache
        static::$cachedSections = $this->parseDocs($docsPath);
        static::$cacheKey = $mtime;
        static::$currentDocsPath = $docsPath;

        return static::$cachedSections;
    }

    protected function getDocsPath(): string
    {
        $detector = app(EnvironmentDetector::class);
        $version = $detector->getStatamicMajorVersion() ?? 6;

        $docsDir = dirname(__DIR__, 3).'/docs';

        // Try version-specific docs first (e.g., statamic-docs-v5.md)
        $versionFile = "{$docsDir}/statamic-docs-v{$version}.md";
        if (file_exists($versionFile)) {
            return $versionFile;
        }

        // Fallback: find any docs file matching the version pattern
        $files = glob("{$docsDir}/statamic-docs-v{$version}*.md");
        if (! empty($files)) {
            return $files[0];
        }

        // Final fallback to v6 beta
        return "{$docsDir}/statamic-docs-v6.0.0-beta.md";
    }

    /**
     * Parse the markdown docs into individual sections.
     *
     * @return array<int, array{title: string, source: string, description: string, content: string}>
     */
    protected function parseDocs(string $path): array
    {
        $content = file_get_contents($path);
        $rawSections = preg_split('/^-{20,}$/m', $content);

        $sections = [];

        foreach ($rawSections as $rawSection) {
            $rawSection = trim($rawSection);
            if (empty($rawSection)) {
                continue;
            }

            $section = $this->parseSection($rawSection);
            if ($section) {
                $sections[] = $section;
            }
        }

        return $sections;
    }

    /**
     * Parse a single section into structured data.
     */
    protected function parseSection(string $raw): ?array
    {
        // Extract title (### heading)
        if (! preg_match('/^###\s+(.+)$/m', $raw, $titleMatch)) {
            return null;
        }

        $title = trim($titleMatch[1]);

        // Extract source URL
        $source = '';
        if (preg_match('/^Source:\s*(.+)$/m', $raw, $sourceMatch)) {
            $source = trim($sourceMatch[1]);
        }

        // Extract description (text between source and first code block)
        $description = '';
        $lines = explode("\n", $raw);
        $inDescription = false;
        $descLines = [];

        foreach ($lines as $line) {
            if (str_starts_with($line, 'Source:')) {
                $inDescription = true;

                continue;
            }
            if ($inDescription) {
                if (str_starts_with($line, '```')) {
                    break;
                }
                $descLines[] = $line;
            }
        }

        $description = trim(implode("\n", $descLines));

        return [
            'title' => $title,
            'source' => $source,
            'description' => $description,
            'content' => $raw,
        ];
    }

    /**
     * Search sections for matching query terms.
     *
     * @return array<int, array{title: string, source: string, description: string, score: int, content: string}>
     */
    protected function searchSections(array $sections, string $query, int $limit): array
    {
        $queryTerms = $this->tokenize($query);
        $scored = [];

        foreach ($sections as $section) {
            $score = $this->scoreSection($section, $queryTerms);
            if ($score > 0) {
                $scored[] = array_merge($section, ['score' => $score]);
            }
        }

        // Sort by score descending
        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $limit);
    }

    /**
     * Score a section based on query term matches.
     */
    protected function scoreSection(array $section, array $queryTerms): int
    {
        $score = 0;
        $titleLower = Str::lower($section['title']);
        $descLower = Str::lower($section['description']);
        $contentLower = Str::lower($section['content']);

        foreach ($queryTerms as $term) {
            // Title matches are worth more
            if (Str::contains($titleLower, $term)) {
                $score += 10;
            }

            // Description matches
            if (Str::contains($descLower, $term)) {
                $score += 5;
            }

            // Content matches (code blocks, etc.)
            $contentMatches = substr_count($contentLower, $term);
            $score += min($contentMatches, 3); // Cap at 3 to avoid over-weighting
        }

        return $score;
    }

    /**
     * Tokenize a query string into searchable terms.
     *
     * @return array<string>
     */
    protected function tokenize(string $query): array
    {
        $query = Str::lower($query);
        $terms = preg_split('/[\s,]+/', $query, -1, PREG_SPLIT_NO_EMPTY);

        // Filter out very short terms
        return array_filter($terms, fn ($term) => strlen($term) >= 2);
    }
}
