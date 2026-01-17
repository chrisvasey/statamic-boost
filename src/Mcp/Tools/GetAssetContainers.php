<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
use Laravel\Mcp\Server\Tool;
use Statamic\Facades\AssetContainer;

#[IsReadOnly]
class GetAssetContainers extends Tool
{
    protected string $description = 'List all Statamic asset containers with their configuration, disk settings, and asset counts.';

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response
    {
        $containers = AssetContainer::all()->map(function ($container) {
            return [
                'handle' => $container->handle(),
                'title' => $container->title(),
                'disk' => $container->diskHandle(),
                'url' => $container->url(),
                'absolute_url' => $container->absoluteUrl(),
                'private' => $container->private(),
                'allow_uploads' => $container->allowUploads(),
                'allow_downloading' => $container->allowDownloading(),
                'allow_moving' => $container->allowMoving(),
                'allow_renaming' => $container->allowRenaming(),
                'create_folders' => $container->createFolders(),
                'source_preset' => $container->sourcePreset(),
                'warm_presets' => $container->warmPresets(),
                'asset_count' => $container->assets()->count(),
            ];
        })->values()->toArray();

        return Response::json([
            'total' => count($containers),
            'containers' => $containers,
        ]);
    }
}
