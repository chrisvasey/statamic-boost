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

];
