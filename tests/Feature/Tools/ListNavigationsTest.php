<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\ListNavigations;
use Laravel\Mcp\Request;
use Statamic\Facades\Nav;

beforeEach(function () {
    Nav::make('main')
        ->title('Main Navigation')
        ->maxDepth(3)
        ->save();
});

afterEach(function () {
    Nav::findByHandle('main')?->delete();
});

it('lists all navigations', function () {
    $tool = new ListNavigations;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['navigations'])->toBeArray();
    expect($data['total'])->toBeGreaterThanOrEqual(1);
});

it('includes navigation handles and titles', function () {
    $tool = new ListNavigations;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $main = collect($data['navigations'])->firstWhere('handle', 'main');

    expect($main)->not->toBeNull();
    expect($main['handle'])->toBe('main');
    expect($main['title'])->toBe('Main Navigation');
    expect($main['max_depth'])->toBe(3);
});

it('returns empty array when no navigations exist', function () {
    Nav::findByHandle('main')?->delete();

    $tool = new ListNavigations;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['navigations'])->toBeArray();
    expect($data['total'])->toBe(0);
});
