<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;
use Statamic\Facades\Addon;

#[IsReadOnly]
class ListAddons extends Tool
{
    protected string $description = 'List all installed Statamic addons with their versions, namespaces, and capabilities.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $addons = Addon::all()->map(function ($addon) {
            return [
                'id' => $addon->id(),
                'name' => $addon->name(),
                'slug' => $addon->slug(),
                'package' => $addon->package(),
                'namespace' => $addon->namespace(),
                'version' => $addon->version(),
                'edition' => $addon->edition(),
                'is_commercial' => $addon->isCommercial(),
                'provider' => $addon->provider(),
                'autoload' => $addon->autoload(),
            ];
        })->values()->toArray();

        return Response::json([
            'total' => count($addons),
            'addons' => $addons,
        ]);
    }
}
