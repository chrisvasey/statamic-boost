<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $boostJsonPath = base_path('boost.json');
    if (File::exists($boostJsonPath)) {
        File::move($boostJsonPath, $boostJsonPath.'.backup');
    }

    // Register a dummy boost:install command for testing
    Artisan::command('boost:install', function () {
        $this->info('Boost installed');
    });
});

afterEach(function () {
    $boostJsonPath = base_path('boost.json');
    File::delete($boostJsonPath);
    if (File::exists($boostJsonPath.'.backup')) {
        File::move($boostJsonPath.'.backup', $boostJsonPath);
    }
});

it('can run with no-interaction flag', function () {
    $this->artisan('statamic-boost:install', ['--no-interaction' => true])
        ->assertSuccessful();
});

it('auto-detects environment in non-interactive mode', function () {
    $this->artisan('statamic-boost:install', ['--no-interaction' => true])
        ->assertSuccessful();

    $boostJson = json_decode(File::get(base_path('boost.json')), true);

    expect($boostJson['environment'])->toBeIn(['statamic', 'hybrid', 'laravel']);
});

it('accepts env option', function () {
    $this->artisan('statamic-boost:install', [
        '--env' => 'statamic',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $boostJson = json_decode(File::get(base_path('boost.json')), true);

    expect($boostJson['environment'])->toBe('statamic');
});

it('rejects invalid env option', function () {
    $this->artisan('statamic-boost:install', [
        '--env' => 'invalid',
        '--no-interaction' => true,
    ])->assertFailed();
});

it('excludes inertia guidelines for statamic environment', function () {
    $this->artisan('statamic-boost:install', [
        '--env' => 'statamic',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $boostJson = json_decode(File::get(base_path('boost.json')), true);

    expect($boostJson)->toHaveKey('exclude_guidelines');
    expect($boostJson['exclude_guidelines'])->toContain('inertia-laravel');
    expect($boostJson['exclude_guidelines'])->toContain('inertia-react');
    expect($boostJson['exclude_guidelines'])->toContain('inertia-vue');
    expect($boostJson['exclude_guidelines'])->toContain('inertia-svelte');
});

it('does not exclude inertia guidelines for hybrid environment', function () {
    $this->artisan('statamic-boost:install', [
        '--env' => 'hybrid',
        '--no-interaction' => true,
    ])->assertSuccessful();

    $boostJson = json_decode(File::get(base_path('boost.json')), true);

    if (isset($boostJson['exclude_guidelines'])) {
        expect($boostJson['exclude_guidelines'])->not->toContain('inertia-laravel');
    }
});

it('removes inertia exclusions when switching from statamic to hybrid', function () {
    // First set to statamic
    $this->artisan('statamic-boost:install', [
        '--env' => 'statamic',
        '--no-interaction' => true,
    ]);

    // Then switch to hybrid
    $this->artisan('statamic-boost:install', [
        '--env' => 'hybrid',
        '--no-interaction' => true,
    ]);

    $boostJson = json_decode(File::get(base_path('boost.json')), true);

    expect($boostJson['environment'])->toBe('hybrid');

    if (isset($boostJson['exclude_guidelines'])) {
        expect($boostJson['exclude_guidelines'])->not->toContain('inertia-laravel');
    }
});
