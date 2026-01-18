<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\SearchStatamicDocs;
use Laravel\Mcp\Request;

it('searches documentation', function () {
    $tool = new SearchStatamicDocs;
    $request = new Request(['queries' => ['antlers']]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data)->toHaveKey('results');
    expect($data['query_count'])->toBe(1);
});

it('requires at least one query', function () {
    $tool = new SearchStatamicDocs;
    $request = new Request(['queries' => []]);

    $response = $tool->handle($request);
    $text = getResponseText($response);

    expect($text)->toContain('At least one query is required');
});

it('supports multiple queries', function () {
    $tool = new SearchStatamicDocs;
    $request = new Request(['queries' => ['collections', 'blueprints']]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    expect($data['query_count'])->toBe(2);
    expect($data['results'])->toHaveKey('collections');
    expect($data['results'])->toHaveKey('blueprints');
});

it('respects limit parameter', function () {
    $tool = new SearchStatamicDocs;
    $request = new Request(['queries' => ['antlers'], 'limit' => 2]);

    $response = $tool->handle($request);
    $data = getResponseData($response);

    $results = $data['results']['antlers'] ?? [];
    expect(count($results))->toBeLessThanOrEqual(2);
});

it('caches parsed sections', function () {
    $tool = new SearchStatamicDocs;

    $request1 = new Request(['queries' => ['collections']]);
    $response1 = $tool->handle($request1);
    $data1 = getResponseData($response1);

    $request2 = new Request(['queries' => ['blueprints']]);
    $response2 = $tool->handle($request2);
    $data2 = getResponseData($response2);

    expect($data1['total_sections'])->toBe($data2['total_sections']);
});
