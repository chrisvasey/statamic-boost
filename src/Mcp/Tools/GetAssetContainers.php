<?php

namespace ChrisVasey\StatamicBoost\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;
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
            // Basic properties that don't require disk access
            $data = [
                'handle' => $container->handle(),
                'title' => $container->title(),
                'disk' => $container->diskHandle(),
            ];

            // Properties that may require disk access - wrap in try-catch
            try {
                $data['private'] = $container->private();
                $data['allow_uploads'] = $container->allowUploads();
                $data['allow_downloading'] = $container->allowDownloading();
                $data['allow_moving'] = $container->allowMoving();
                $data['allow_renaming'] = $container->allowRenaming();
                $data['create_folders'] = $container->createFolders();
                $data['source_preset'] = $container->sourcePreset();
                $data['warm_presets'] = $container->warmPresets();
                $data['url'] = $container->url();
                $data['absolute_url'] = $container->absoluteUrl();
                $data['asset_count'] = $container->assets()->count();
            } catch (\Exception $e) {
                $data['disk_error'] = "Unable to access disk '{$container->diskHandle()}': {$e->getMessage()}";
            }

            return $data;
        })->values()->toArray();

        return Response::json([
            'total' => count($containers),
            'containers' => $containers,
        ]);
    }
}
