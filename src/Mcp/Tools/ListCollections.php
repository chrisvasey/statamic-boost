<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Statamic\Facades\Collection;

#[IsReadOnly]
class ListCollections extends Tool
{
    protected string $description = 'List all Statamic collections with their handles, titles, routes, and blueprint information.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $collections = Collection::all()->map(function ($collection) {
            $blueprints = $collection->entryBlueprints()->map(fn ($bp) => $bp->handle())->values();

            return [
                'handle' => $collection->handle(),
                'title' => $collection->title(),
                'routes' => $collection->routes()->toArray(),
                'url' => $collection->url(),
                'dated' => $collection->dated(),
                'sort_direction' => $collection->sortDirection(),
                'blueprints' => $blueprints->toArray(),
                'entry_count' => $collection->queryEntries()->count(),
                'structure' => $collection->hasStructure() ? $collection->structure()?->handle() : null,
                'mount' => $collection->mount()?->id(),
                'taxonomy_handles' => $collection->taxonomies()->map->handle()->values()->toArray(),
            ];
        })->values()->toArray();

        return Response::json([
            'total' => count($collections),
            'collections' => $collections,
        ]);
    }
}
