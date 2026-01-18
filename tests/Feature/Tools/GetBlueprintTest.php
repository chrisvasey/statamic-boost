<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\GetBlueprint;
use Laravel\Mcp\Request;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;

beforeEach(function () {
    Collection::make('posts')
        ->title('Posts')
        ->save();

    Blueprint::make('post')
        ->setNamespace('collections.posts')
        ->setContents([
            'sections' => [
                'main' => [
                    'fields' => [
                        ['handle' => 'title', 'field' => ['type' => 'text', 'display' => 'Title']],
                        ['handle' => 'content', 'field' => ['type' => 'bard', 'display' => 'Content']],
                    ],
                ],
            ],
        ])
        ->save();
});

afterEach(function () {
    Collection::findByHandle('posts')?->delete();
});

it('returns blueprint fields', function () {
    $tool = new GetBlueprint;
    $request = new Request([
        'namespace' => 'collections.posts',
        'handle' => 'post',
    ]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['handle'])->toBe('post');
    expect($data['fields'])->toBeArray();
});

it('returns error for non-existent blueprint', function () {
    $tool = new GetBlueprint;
    $request = new Request([
        'namespace' => 'collections.posts',
        'handle' => 'nonexistent',
    ]);

    $response = $tool->handle($request);
    $text = getResponseText($response);

    expect($text)->toContain('not found');
});

it('requires namespace and handle', function () {
    $tool = new GetBlueprint;
    $request = new Request([]);

    $response = $tool->handle($request);
    $text = getResponseText($response);

    expect($text)->toBeString();
});
