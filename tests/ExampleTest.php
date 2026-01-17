<?php

use ChrisVasey\StatamicBoost\EnvironmentDetector;
use ChrisVasey\StatamicBoost\StatamicBoostServiceProvider;

it('registers the service provider', function () {
    expect(class_exists(StatamicBoostServiceProvider::class))->toBeTrue();
});

it('binds environment detector as singleton', function () {
    $detector1 = app(EnvironmentDetector::class);
    $detector2 = app(EnvironmentDetector::class);

    expect($detector1)->toBe($detector2);
});

it('registers statamic tools in boost config', function () {
    $include = config('boost.mcp.tools.include', []);

    expect($include)->toContain('ChrisVasey\StatamicBoost\Mcp\Tools\ListCollections');
});
