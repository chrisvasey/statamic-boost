# Statamic Boost

Statamic-specific MCP tools and AI guidelines for [Laravel Boost](https://github.com/laravel/boost). Provides AI assistants with deep knowledge of your Statamic site's structure, content, and configuration.

## Features

- **12 MCP Tools** for querying Statamic content, blueprints, and configuration
- **AI Guidelines** for Antlers templating, blueprints, and Statamic best practices
- **Environment Detection** automatically excludes database tools for flat-file sites
- **Seamless Integration** with Laravel Boost's MCP server

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12
- Statamic 5 or 6
- Laravel Boost 1.0+

## Installation

```bash
composer require chrisvasey/statamic-boost --dev
```

Then run the Boost installer to configure your AI tools:

```bash
php artisan boost:install
```

## Available MCP Tools

| Tool | Description |
|------|-------------|
| `statamic_list_collections` | List all collections with routes, blueprints, and entry counts |
| `statamic_get_collection_entries` | Query entries with filtering by collection, status, and limit |
| `statamic_get_blueprint` | Get field definitions for any blueprint |
| `statamic_list_navigations` | List navigation trees and their structure |
| `statamic_list_globals` | List global sets with their current values |
| `statamic_list_taxonomies` | List taxonomies with term counts |
| `statamic_get_asset_containers` | List asset containers and their configuration |
| `statamic_list_forms` | List forms with fields and submission counts |
| `statamic_list_fieldtypes` | List all available fieldtypes |
| `statamic_list_addons` | List installed addons with versions |
| `statamic_stache_info` | Get Stache cache status and store information |
| `statamic_search_docs` | Search Statamic documentation |

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=statamic-boost-config
```

### Exclude Tools

Disable specific tools in `config/statamic-boost.php`:

```php
'tools' => [
    'exclude' => [
        \ChrisVasey\StatamicBoost\Mcp\Tools\StacheInfo::class,
    ],
],
```

### Environment Detection

By default, Statamic Boost detects whether your site is "Statamic-centric" (pure flat-file) or a hybrid Laravel app. For Statamic-centric sites, it automatically excludes Laravel Boost's database tools since they're not relevant.

A site is considered Statamic-centric when:
- No custom Eloquent models (beyond User.php)
- Runway addon is not installed
- Users are stored as flat files

Disable auto-detection:

```php
'auto_detect' => false,
```

## AI Guidelines

Statamic Boost includes guidelines for AI assistants covering:

- Antlers templating syntax and patterns
- Blueprint field definitions
- Collection and taxonomy configuration
- Common tags and modifiers
- Statamic CLI commands (`php please`)
- Best practices for Statamic development

Guidelines are automatically included when you run `php artisan boost:install`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
