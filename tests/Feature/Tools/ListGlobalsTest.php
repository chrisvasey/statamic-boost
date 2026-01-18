<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\ListGlobals;
use Laravel\Mcp\Request;
use Statamic\Facades\GlobalSet;

beforeEach(function () {
    GlobalSet::make('site')
        ->title('Site Settings')
        ->save();
});

afterEach(function () {
    GlobalSet::findByHandle('site')?->delete();
});

it('lists all global sets', function () {
    $tool = new ListGlobals;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['globals'])->toBeArray();
    expect($data['total'])->toBeGreaterThanOrEqual(1);
});

it('includes global handles and titles', function () {
    $tool = new ListGlobals;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $site = collect($data['globals'])->firstWhere('handle', 'site');

    expect($site)->not->toBeNull();
    expect($site['handle'])->toBe('site');
    expect($site['title'])->toBe('Site Settings');
});

it('can exclude values', function () {
    $tool = new ListGlobals;
    $request = new Request(['include_values' => false]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $site = collect($data['globals'])->firstWhere('handle', 'site');

    expect($site)->not->toHaveKey('variables');
});

it('returns empty array when no globals exist', function () {
    GlobalSet::findByHandle('site')?->delete();

    $tool = new ListGlobals;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['globals'])->toBeArray();
    expect($data['total'])->toBe(0);
});
