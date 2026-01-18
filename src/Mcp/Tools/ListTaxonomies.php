<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Statamic\Facades\Taxonomy;

#[IsReadOnly]
class ListTaxonomies extends Tool
{
    protected string $description = 'List all Statamic taxonomies with their handles, titles, and term counts.';

    public function schema(JsonSchema $schema): array
    {
        return [
            'include_terms' => $schema->boolean()
                ->description('Include a list of terms for each taxonomy (default: false)')
                ->default(false),
            'terms_limit' => $schema->integer()
                ->description('Maximum number of terms to include per taxonomy (default: 20)')
                ->default(20),
        ];
    }

    public function handle(Request $request): Response
    {
        $includeTerms = $request->get('include_terms', false);
        $termsLimit = $request->get('terms_limit', 20);

        $taxonomies = Taxonomy::all()->map(function ($taxonomy) use ($includeTerms, $termsLimit) {
            $blueprints = $taxonomy->termBlueprints()->map(fn ($bp) => $bp->handle())->values();

            $data = [
                'handle' => $taxonomy->handle(),
                'title' => $taxonomy->title(),
                'uri' => $taxonomy->uri(),
                'url' => $taxonomy->url(),
                'blueprints' => $blueprints->toArray(),
                'term_count' => $taxonomy->queryTerms()->count(),
                'collections' => $taxonomy->collections()->map->handle()->values()->toArray(),
            ];

            if ($includeTerms) {
                $data['terms'] = $taxonomy->queryTerms()
                    ->limit($termsLimit)
                    ->get()
                    ->map(function ($term) {
                        return [
                            'id' => $term->id(),
                            'title' => $term->title(),
                            'slug' => $term->slug(),
                            'uri' => $term->uri(),
                            'entries_count' => $term->entriesCount(),
                        ];
                    })->values()->toArray();
            }

            return $data;
        })->values()->toArray();

        return Response::json([
            'total' => count($taxonomies),
            'taxonomies' => $taxonomies,
        ]);
    }
}
