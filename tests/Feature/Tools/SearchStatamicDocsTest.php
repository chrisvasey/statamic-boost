<?php

use ChrisVasey\StatamicBoost\EnvironmentDetector;
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

it('searches the complete bundle for the installed major', function (int $major) {
    $detector = Mockery::mock(EnvironmentDetector::class);
    $detector->shouldReceive('getStatamicMajorVersion')->andReturn($major);
    app()->instance(EnvironmentDetector::class, $detector);
    $manifest = json_decode(file_get_contents(__DIR__.'/../../../docs/upstream.json'), true);
    $version = $manifest['versions'][$major];

    $data = getResponseData((new SearchStatamicDocs)->handle(new Request([
        'queries' => ['blueprints', 'show_future'],
    ])));

    expect($data['total_sections'])->toBe($version['pages']);
    foreach ($data['results'] as $results) {
        expect($results)->not->toBeEmpty();
        foreach ($results as $result) {
            expect($result['source'])->toStartWith('https://github.com/statamic/docs/blob/'.$version['commit'].'/');
        }
    }
})->with([5, 6]);

it('does not serve another major when its documentation is unavailable', function () {
    $detector = Mockery::mock(EnvironmentDetector::class);
    $detector->shouldReceive('getStatamicMajorVersion')->andReturn(7);
    app()->instance(EnvironmentDetector::class, $detector);

    $response = (new SearchStatamicDocs)->handle(new Request(['queries' => ['collections']]));

    expect(getResponseText($response))->toContain('Documentation file not found');
});

it('parses separate documentation pages with Windows line endings', function () {
    $path = tempnam(sys_get_temp_dir(), 'statamic-docs-crlf-');

    try {
        file_put_contents($path, "### First\r\nSource: https://example.com/first\r\n\r\nFirst page.\r\n\r\n--------------------\r\n\r\n### Second\r\nSource: https://example.com/second\r\n\r\nSecond page.\r\n");
        $sections = (new ReflectionMethod(SearchStatamicDocs::class, 'parseDocs'))->invoke(new SearchStatamicDocs, $path);

        expect(array_column($sections, 'title'))->toBe(['First', 'Second'])
            ->and($sections[1]['source'])->toBe('https://example.com/second');
    } finally {
        unlink($path);
    }
});
