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
