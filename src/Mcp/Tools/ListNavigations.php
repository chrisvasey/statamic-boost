<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Statamic\Facades\Nav;

#[IsReadOnly]
class ListNavigations extends Tool
{
    protected string $description = 'List all Statamic navigation trees with their handles, titles, and tree structure.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $navs = Nav::all()->map(function ($nav) {
            return [
                'handle' => $nav->handle(),
                'title' => $nav->title(),
                'max_depth' => $nav->maxDepth(),
                'collections' => $nav->collections()->map->handle()->values()->toArray(),
                'trees' => $nav->trees()->map(function ($tree) {
                    return [
                        'site' => $tree->site()->handle(),
                        'tree' => $this->flattenTree($tree->tree()),
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return Response::json([
            'total' => count($navs),
            'navigations' => $navs,
        ]);
    }

    protected function flattenTree(array $items, int $depth = 0): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = [
                'id' => $item['id'] ?? null,
                'title' => $item['title'] ?? null,
                'url' => $item['url'] ?? null,
                'entry' => $item['entry'] ?? null,
                'depth' => $depth,
                'children_count' => count($item['children'] ?? []),
            ];

            if (! empty($item['children'])) {
                $result = array_merge($result, $this->flattenTree($item['children'], $depth + 1));
            }
        }

        return $result;
    }
}
