<?php

namespace ChrisVasey\StatamicBoost\Scripts;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class DocsBundle
{
    /** @var array<string, list<string>> */
    private const COLLECTIONS = [
        '5' => ['docs', 'extending-docs', 'reference', 'repositories', 'tags', 'modifiers', 'fieldtypes', 'variables', 'widgets', 'tips', 'troubleshooting'],
        '6' => ['pages', 'resource_apis', 'tags', 'modifiers', 'fieldtypes', 'variables', 'widgets', 'tips', 'troubleshooting'],
    ];

    /** @var list<string> */
    private const TEXT_FIELDS = ['intro', 'description', 'overview', 'options_content', 'variables_content'];

    /** @var list<string> */
    private const REFERENCE_FIELDS = ['parameters', 'variables', 'options', 'shape', 'filters', 'other', 'types', 'modifier_types', 'class', 'pro', 'added_in'];

    /**
     * @return array{major: string, commit: string, pages: int, sha256: string}
     */
    public function write(string $sourceDirectory, string $major, string $commit, string $outputFile): array
    {
        if (! isset(self::COLLECTIONS[$major])) {
            throw new RuntimeException('The documentation major must be 5 or 6.');
        }

        if (! preg_match('/\A[0-9a-f]{40}\z/i', $commit)) {
            throw new RuntimeException('The source commit must be a full 40-character Git SHA.');
        }

        $source = realpath($sourceDirectory);

        if ($source === false || ! is_dir($source)) {
            throw new RuntimeException('The source directory does not exist.');
        }

        $source = str_replace(DIRECTORY_SEPARATOR, '/', $source);
        $commit = strtolower($commit);
        $sections = [];

        foreach (self::COLLECTIONS[$major] as $collection) {
            $directory = $source.'/content/collections/'.$collection;
            $this->requireDirectory($source, $directory);
            $files = $this->markdownFiles($directory);
            $count = count($sections);

            foreach ($files as $file) {
                $section = $this->section($file, substr($file, strlen($source) + 1), $commit);

                if ($section !== null) {
                    $sections[] = $section;
                }
            }

            if (count($sections) === $count) {
                throw new RuntimeException("Incomplete snapshot: no published documentation in {$collection}.");
            }
        }

        $bundle = implode("\n\n--------------------\n\n", $sections)."\n";
        $this->replaceOutput($outputFile, $bundle);

        return [
            'major' => $major,
            'commit' => $commit,
            'pages' => count($sections),
            'sha256' => hash('sha256', $bundle),
        ];
    }

    private function requireDirectory(string $source, string $directory): void
    {
        $resolved = realpath($directory);

        if ($resolved !== false) {
            $resolved = str_replace(DIRECTORY_SEPARATOR, '/', $resolved);
        }

        if ($resolved === false || ! is_dir($resolved) || $resolved !== $directory || ! str_starts_with($resolved, $source.'/')) {
            throw new RuntimeException("Incomplete snapshot: missing or linked documentation directory {$directory}.");
        }
    }

    /** @return list<string> */
    private function markdownFiles(string $directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
            RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $file) {
            if ($file->isLink()) {
                throw new RuntimeException("Linked documentation input is not allowed: {$file->getPathname()}.");
            }

            if ($file->isFile() && $file->getExtension() === 'md') {
                $files[] = $file->getPathname();
            }
        }

        sort($files, SORT_STRING);

        return $files;
    }

    private function section(string $file, string $relativePath, string $commit): ?string
    {
        $raw = file_get_contents($file);

        if ($raw === false || ! preg_match('//u', $raw)) {
            throw new RuntimeException("Unreadable or invalid UTF-8 documentation: {$relativePath}.");
        }

        $raw = str_replace(["\r\n", "\r"], "\n", $raw);
        $raw = preg_replace('/\A\xEF\xBB\xBF/', '', $raw);

        if (! preg_match('/\A---\n(.*?)\n---(?:\n|\z)(.*)\z/s', $raw, $parts)) {
            throw new RuntimeException("Missing or incomplete frontmatter: {$relativePath}.");
        }

        try {
            $data = Yaml::parse($parts[1], Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
        } catch (ParseException $exception) {
            throw new RuntimeException("Invalid frontmatter in {$relativePath}: {$exception->getMessage()}", previous: $exception);
        }

        if (! is_array($data) || array_is_list($data)) {
            throw new RuntimeException("Frontmatter must be a mapping: {$relativePath}.");
        }

        if (isset($data['published']) && ! is_bool($data['published'])) {
            throw new RuntimeException("Invalid published status: {$relativePath}.");
        }

        if (($data['published'] ?? true) === false || ($data['draft'] ?? false) === true || ($data['blueprint'] ?? null) === 'link' || ! empty($data['redirect']) || ! empty($data['link'])) {
            return null;
        }

        $title = $data['title'] ?? pathinfo($file, PATHINFO_FILENAME);

        if (! is_string($title) || trim($title) === '' || str_contains($title, "\n")) {
            throw new RuntimeException("Invalid documentation title: {$relativePath}.");
        }

        $content = [];

        foreach (self::TEXT_FIELDS as $field) {
            if (! isset($data[$field])) {
                continue;
            }

            if (! is_string($data[$field])) {
                throw new RuntimeException("Invalid {$field} text: {$relativePath}.");
            }

            if (trim($data[$field]) !== '') {
                $content[] = trim($data[$field]);
            }
        }

        if (trim($parts[2]) !== '') {
            $content[] = trim($parts[2]);
        }

        $reference = array_intersect_key($data, array_flip(self::REFERENCE_FIELDS));

        if ($reference !== []) {
            ksort($reference, SORT_STRING);
            $yaml = rtrim(Yaml::dump($reference, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
            preg_match_all('/`+/', $yaml, $backticks);
            $fence = str_repeat('`', max([3, ...array_map(fn (string $run): int => strlen($run) + 1, $backticks[0])]));
            $content[] = "## Reference\n\n{$fence}yaml\n{$yaml}\n{$fence}";
        }

        if ($content === []) {
            return null;
        }

        $body = implode("\n\n", array_unique($content));
        // SearchStatamicDocs treats a bare line of 20+ hyphens as a page boundary.
        $body = preg_replace('/^(-{20,})$/m', '$1 ', $body);
        $url = 'https://github.com/statamic/docs/blob/'.$commit.'/'.implode('/', array_map('rawurlencode', explode('/', $relativePath)));

        return '### '.trim($title)."\nSource: {$url}\n\n{$body}";
    }

    private function replaceOutput(string $outputFile, string $bundle): void
    {
        $directory = realpath(dirname($outputFile));

        if ($directory === false || ! is_dir($directory) || is_link($outputFile) || (file_exists($outputFile) && ! is_file($outputFile))) {
            throw new RuntimeException('The output must be a regular file in an existing directory.');
        }

        $temporary = tempnam($directory, '.docs-bundle-');

        if ($temporary === false) {
            throw new RuntimeException('Could not create a temporary documentation bundle.');
        }

        try {
            if (file_put_contents($temporary, $bundle) !== strlen($bundle) || ! rename($temporary, $outputFile)) {
                throw new RuntimeException('Could not replace the documentation bundle.');
            }
        } finally {
            if (file_exists($temporary)) {
                unlink($temporary);
            }
        }
    }
}
