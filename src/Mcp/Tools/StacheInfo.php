<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;
use Statamic\Facades\Stache;

#[IsReadOnly]
class StacheInfo extends Tool
{
    protected string $description = 'Get information about Statamic\'s Stache (flat-file content cache) including stores, item counts, and cache status.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $stache = Stache::getFacadeRoot();

        $stores = collect($stache->stores())->map(function ($store) {
            try {
                $key = $store->key();
                $directory = method_exists($store, 'directory') ? $store->directory() : null;

                return [
                    'key' => $key,
                    'directory' => $directory,
                ];
            } catch (\Exception $e) {
                return null;
            }
        })->filter()->values()->toArray();

        return Response::json([
            'store_count' => count($stores),
            'stores' => $stores,
            'cache_path' => config('statamic.stache.path', storage_path('statamic/stache')),
            'lock_enabled' => config('statamic.stache.lock.enabled', true),
            'watcher_enabled' => config('statamic.stache.watcher', true),
        ]);
    }
}
