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
            $this->commands([
                Console\InstallCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/statamic-boost.php' => config_path('statamic-boost.php'),
            ], 'statamic-boost-config');

            $this->publishes([
                __DIR__.'/../.ai' => base_path('.ai'),
            ], 'statamic-boost-guidelines');
        }

        $this->applyEnvironmentConfiguration();
    }

    protected function applyEnvironmentConfiguration(): void
    {
        $environment = $this->determineEnvironment();

        // Register Statamic tools (unless in laravel-only mode)
        if ($environment !== 'laravel') {
            $this->registerStatamicTools();
        }

        // Apply environment-specific tool excludes
        $this->applyEnvironmentExcludes($environment);
    }

    protected function determineEnvironment(): string
    {
        // First check boost.json for persisted environment setting
        $boostJsonPath = base_path('boost.json');
        if (file_exists($boostJsonPath)) {
            $boostConfig = json_decode(file_get_contents($boostJsonPath), true);
            if (isset($boostConfig['environment'])) {
                return $boostConfig['environment'];
            }
        }

        // Fall back to config value
        $environment = config('statamic-boost.environment', 'hybrid');

        // If hybrid and auto-detect is enabled, check if we should treat as statamic-centric
        if ($environment === 'hybrid' && config('statamic-boost.auto_detect', true)) {
            $detector = $this->app->make(EnvironmentDetector::class);
            if ($detector->isStatamicCentric()) {
                return 'statamic';
            }
        }

        return $environment;
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

    protected function applyEnvironmentExcludes(string $environment): void
    {
        $environmentExcludes = config("statamic-boost.environment_excludes.{$environment}", []);

        if (empty($environmentExcludes)) {
            return;
        }

        $existingExclude = config('boost.mcp.tools.exclude', []);
        config(['boost.mcp.tools.exclude' => array_merge($existingExclude, $environmentExcludes)]);
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
