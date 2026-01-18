<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\ListTaxonomies;
use Laravel\Mcp\Request;
use Statamic\Facades\Taxonomy;

beforeEach(function () {
    Taxonomy::make('tags')
        ->title('Tags')
        ->save();
});

afterEach(function () {
    Taxonomy::findByHandle('tags')?->delete();
});

it('lists all taxonomies', function () {
    $tool = new ListTaxonomies;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['taxonomies'])->toBeArray();
    expect($data['total'])->toBeGreaterThanOrEqual(1);
});

it('includes taxonomy handles and titles', function () {
    $tool = new ListTaxonomies;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $tags = collect($data['taxonomies'])->firstWhere('handle', 'tags');

    expect($tags)->not->toBeNull();
    expect($tags['handle'])->toBe('tags');
    expect($tags['title'])->toBe('Tags');
});

it('returns empty array when no taxonomies exist', function () {
    Taxonomy::findByHandle('tags')?->delete();

    $tool = new ListTaxonomies;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['taxonomies'])->toBeArray();
    expect($data['total'])->toBe(0);
});
