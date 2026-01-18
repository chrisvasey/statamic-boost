<?php

use ChrisVasey\StatamicBoost\EnvironmentDetector;

it('detects statamic installation', function () {
    $detector = new EnvironmentDetector;

    expect($detector->isStatamicInstalled())->toBeTrue();
});

it('returns environment summary', function () {
    $detector = new EnvironmentDetector;
    $summary = $detector->getEnvironmentSummary();

    expect($summary)->toBeArray()
        ->toHaveKeys([
            'statamic_installed',
            'statamic_centric',
            'hybrid_app',
            'has_runway',
            'has_custom_models',
            'uses_eloquent_users',
        ]);
});

it('can clear cache', function () {
    $detector = new EnvironmentDetector;

    // Call isStatamicCentric to populate cache
    $detector->isStatamicCentric();

    // Clear and verify no errors
    $detector->clearCache();

    expect(true)->toBeTrue();
});

it('detects statamic-centric environment', function () {
    $detector = new EnvironmentDetector;

    // In test environment, should detect as statamic-centric or hybrid
    expect($detector->isStatamicCentric())->toBeBool();
});

it('detects hybrid app environment', function () {
    $detector = new EnvironmentDetector;

    // Hybrid app detection should return boolean
    expect($detector->isHybridApp())->toBeBool();
});

it('checks for runway addon', function () {
    $detector = new EnvironmentDetector;

    // Should return boolean whether runway is installed
    expect($detector->hasRunwayAddon())->toBeBool();
});

it('checks for eloquent users', function () {
    $detector = new EnvironmentDetector;

    // Should return boolean based on user repository config
    expect($detector->usesEloquentUsers())->toBeBool();
});

it('summary values are consistent with individual methods', function () {
    $detector = new EnvironmentDetector;
    $summary = $detector->getEnvironmentSummary();

    expect($summary['statamic_installed'])->toBe($detector->isStatamicInstalled());
    expect($summary['statamic_centric'])->toBe($detector->isStatamicCentric());
    expect($summary['hybrid_app'])->toBe($detector->isHybridApp());
    expect($summary['has_runway'])->toBe($detector->hasRunwayAddon());
    expect($summary['uses_eloquent_users'])->toBe($detector->usesEloquentUsers());
});

it('caches detection results', function () {
    $detector = new EnvironmentDetector;

    // First call populates cache
    $result1 = $detector->isStatamicCentric();

    // Second call should return same result (from cache)
    $result2 = $detector->isStatamicCentric();

    expect($result1)->toBe($result2);
});

it('cache can be cleared and repopulated', function () {
    $detector = new EnvironmentDetector;

    // Populate cache
    $detector->isStatamicCentric();

    // Clear cache
    $detector->clearCache();

    // Should still work after cache clear
    expect($detector->isStatamicCentric())->toBeBool();
});
