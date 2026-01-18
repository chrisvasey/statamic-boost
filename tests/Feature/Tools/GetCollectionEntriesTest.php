<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\GetCollectionEntries;
use Laravel\Mcp\Request;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;

beforeEach(function () {
    Collection::make('articles')
        ->title('Articles')
        ->save();

    Entry::make()
        ->collection('articles')
        ->slug('test-article')
        ->data(['title' => 'Test Article'])
        ->save();
});

afterEach(function () {
    Entry::query()->where('collection', 'articles')->get()->each->delete();
    Collection::findByHandle('articles')?->delete();
});

it('returns entries from a collection', function () {
    $tool = new GetCollectionEntries;
    $request = new Request(['collection' => 'articles']);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['entries'])->toBeArray();
    expect($data['total'])->toBeGreaterThanOrEqual(1);
});

it('returns error for non-existent collection', function () {
    $tool = new GetCollectionEntries;
    $request = new Request(['collection' => 'nonexistent']);

    $response = $tool->handle($request);
    $text = getResponseText($response);

    expect($text)->toContain('not found');
    expect($text)->toContain('nonexistent');
});

it('respects limit parameter', function () {
    for ($i = 0; $i < 5; $i++) {
        Entry::make()
            ->collection('articles')
            ->slug("article-{$i}")
            ->data(['title' => "Article {$i}"])
            ->save();
    }

    $tool = new GetCollectionEntries;
    $request = new Request(['collection' => 'articles', 'limit' => 2]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['total'])->toBe(2);
});

it('returns all entries when no collection specified', function () {
    $tool = new GetCollectionEntries;
    $request = new Request([]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['entries'])->toBeArray();
});
