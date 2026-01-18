<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\GetAssetContainers;
use Laravel\Mcp\Request;
use Statamic\Facades\AssetContainer;

beforeEach(function () {
    if (! class_exists(\Statamic\Facades\AssetContainer::class)) {
        $this->markTestSkipped('Statamic AssetContainer facade not available');
    }
});

afterEach(function () {
    try {
        AssetContainer::findByHandle('images')?->delete();
        AssetContainer::findByHandle('broken')?->delete();
    } catch (\Exception $e) {
        // Ignore cleanup errors
    }
});

it('lists all asset containers', function () {
    try {
        AssetContainer::make('images')
            ->title('Images')
            ->disk('local')
            ->save();
    } catch (\Exception $e) {
        $this->markTestSkipped('Unable to create asset container');
    }

    $tool = new GetAssetContainers;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['containers'])->toBeArray();
    expect($data['total'])->toBeGreaterThanOrEqual(1);
});

it('includes container handles and titles', function () {
    try {
        AssetContainer::make('images')
            ->title('Images')
            ->disk('local')
            ->save();
    } catch (\Exception $e) {
        $this->markTestSkipped('Unable to create asset container');
    }

    $tool = new GetAssetContainers;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $images = collect($data['containers'])->firstWhere('handle', 'images');

    expect($images)->not->toBeNull();
    expect($images['handle'])->toBe('images');
    expect($images['title'])->toBe('Images');
});

it('returns array for containers response', function () {
    $tool = new GetAssetContainers;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['containers'])->toBeArray();
    expect($data)->toHaveKey('total');
});
