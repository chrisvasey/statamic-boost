<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Environment Mode
    |--------------------------------------------------------------------------
    |
    | Determines how Statamic Boost configures guidelines and tools.
    |
    | Options:
    | - 'statamic' : Statamic-only (flat-file CMS, excludes DB/Eloquent guidelines)
    | - 'hybrid'   : Laravel + Statamic (full guidelines, all tools)
    | - 'laravel'  : Laravel-only (excludes Statamic guidelines)
    |
    | Run `php artisan statamic-boost:install` to configure this interactively.
    |
    */

    'environment' => env('STATAMIC_BOOST_ENV', 'hybrid'),

    /*
    |--------------------------------------------------------------------------
    | Auto-Detect Environment
    |--------------------------------------------------------------------------
    |
    | When enabled and environment is 'hybrid', Statamic Boost will further
    | detect whether this is a Statamic-centric site (flat-file only) and
    | adjust database tool availability accordingly.
    |
    */

    'auto_detect' => true,

    /*
    |--------------------------------------------------------------------------
    | Tools Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which Statamic MCP tools should be available. By default,
    | all Statamic tools are included. You can exclude specific tools or
    | add additional custom tools.
    |
    */

    'tools' => [
        // Tools to exclude from registration
        'exclude' => [
            // \ChrisVasey\StatamicBoost\Mcp\Tools\StacheInfo::class,
        ],

        // Additional tools to include
        'include' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Statamic-Centric Excludes
    |--------------------------------------------------------------------------
    |
    | When auto_detect is enabled and the site is detected as Statamic-centric
    | (no custom Eloquent models, no Runway addon), these Laravel Boost tools
    | will be automatically excluded as they're not relevant.
    |
    */

    'statamic_centric_excludes' => [
        \Laravel\Boost\Mcp\Tools\DatabaseSchema::class,
        \Laravel\Boost\Mcp\Tools\DatabaseQuery::class,
        \Laravel\Boost\Mcp\Tools\DatabaseConnections::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment-Specific Tool Excludes
    |--------------------------------------------------------------------------
    |
    | These tools are automatically excluded based on the environment setting.
    |
    */

    'environment_excludes' => [
        // Tools excluded when environment is 'statamic' (flat-file only)
        'statamic' => [
            \Laravel\Boost\Mcp\Tools\DatabaseSchema::class,
            \Laravel\Boost\Mcp\Tools\DatabaseQuery::class,
            \Laravel\Boost\Mcp\Tools\DatabaseConnections::class,
        ],

        // Tools excluded when environment is 'laravel' (no Statamic)
        'laravel' => [
            \ChrisVasey\StatamicBoost\Mcp\Tools\ListCollections::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\GetCollectionEntries::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\GetBlueprint::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\ListNavigations::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\ListGlobals::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\ListTaxonomies::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\GetAssetContainers::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\ListForms::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\ListFieldtypes::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\ListAddons::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\StacheInfo::class,
            \ChrisVasey\StatamicBoost\Mcp\Tools\SearchStatamicDocs::class,
        ],

        // No tools excluded when environment is 'hybrid'
        'hybrid' => [],
    ],

];
