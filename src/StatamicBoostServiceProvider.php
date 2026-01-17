<?php

namespace ChrisVasey\StatamicBoost;

use Illuminate\Support\ServiceProvider;

class StatamicBoostServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/statamic-boost.php', 'statamic-boost');

        $this->app->singleton(EnvironmentDetector::class, fn () => new EnvironmentDetector);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/statamic-boost.php' => config_path('statamic-boost.php'),
            ], 'statamic-boost-config');

            $this->publishes([
                __DIR__.'/../.ai' => base_path('.ai'),
            ], 'statamic-boost-guidelines');
        }

        $this->registerStatamicTools();
        $this->conditionallyExcludeDatabaseTools();
    }

    protected function registerStatamicTools(): void
    {
        $tools = $this->getStatamicTools();
        $excluded = config('statamic-boost.tools.exclude', []);
        $included = config('statamic-boost.tools.include', []);

        $toolsToRegister = array_merge(
            array_diff($tools, $excluded),
            $included
        );

        // Merge our tools into Laravel Boost's include list
        $existingInclude = config('boost.mcp.tools.include', []);
        config(['boost.mcp.tools.include' => array_merge($existingInclude, $toolsToRegister)]);
    }

    protected function conditionallyExcludeDatabaseTools(): void
    {
        if (! config('statamic-boost.auto_detect', true)) {
            return;
        }

        $detector = $this->app->make(EnvironmentDetector::class);

        if ($detector->isStatamicCentric()) {
            $excludes = config('statamic-boost.statamic_centric_excludes', []);
            $existingExclude = config('boost.mcp.tools.exclude', []);
            config(['boost.mcp.tools.exclude' => array_merge($existingExclude, $excludes)]);
        }
    }

    protected function getStatamicTools(): array
    {
        return [
            Mcp\Tools\ListCollections::class,
            Mcp\Tools\GetCollectionEntries::class,
            Mcp\Tools\GetBlueprint::class,
            Mcp\Tools\ListNavigations::class,
            Mcp\Tools\ListGlobals::class,
            Mcp\Tools\ListTaxonomies::class,
            Mcp\Tools\GetAssetContainers::class,
            Mcp\Tools\ListForms::class,
            Mcp\Tools\ListFieldtypes::class,
            Mcp\Tools\ListAddons::class,
            Mcp\Tools\StacheInfo::class,
            Mcp\Tools\SearchStatamicDocs::class,
        ];
    }
}
