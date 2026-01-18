<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\ListCollections;
use Laravel\Mcp\Request;
use Statamic\Facades\Collection;

beforeEach(function () {
    Collection::make('blog')
        ->title('Blog')
        ->routes('/blog/{slug}')
        ->save();
});

afterEach(function () {
    Collection::findByHandle('blog')?->delete();
});

it('lists all collections', function () {
    $tool = new ListCollections;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['collections'])->toBeArray();
    expect($data['total'])->toBeGreaterThanOrEqual(1);
});

it('includes collection handles and titles', function () {
    $tool = new ListCollections;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $blog = collect($data['collections'])->firstWhere('handle', 'blog');

    expect($blog)->not->toBeNull();
    expect($blog['handle'])->toBe('blog');
    expect($blog['title'])->toBe('Blog');
});

it('returns empty array when no collections exist', function () {
    Collection::findByHandle('blog')?->delete();
    Collection::findByHandle('pages')?->delete();

    $tool = new ListCollections;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['collections'])->toBeArray();
});
