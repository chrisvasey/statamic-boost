# Changelog

All notable changes to `statamic-boost` will be documented in this file.

## v1.1.1 - 2026-01-21

Added link to [cboxdk/statamic-mcp](https://github.com/cboxdk/statamic-mcp) in README.

## v1.1.0 - setup as Statamic Addon - 2026-01-21

- Converted to official Statamic addon structure using `php please make:addon`
- ServiceProvider now extends `Statamic\Providers\AddonServiceProvider` instead of Laravel's base ServiceProvider
- Uses `bootAddon()` method for addon-specific boot logic
- Added `extra.statamic` section to composer.json for proper addon discovery

## v1.1.0 - 2026-01-20

### Changed

- Converted to official Statamic addon structure using `php please make:addon`
- ServiceProvider now extends `Statamic\Providers\AddonServiceProvider` instead of Laravel's base ServiceProvider
- Uses `bootAddon()` method for addon-specific boot logic
- Added `extra.statamic` section to composer.json for proper addon discovery

## v1.0.0 - 2026-01-19

Initial release of Statamic Boost — MCP tools and AI guidelines for Statamic sites, extending Laravel Boost.

### Features

- **12 MCP Tools** for querying Statamic content and configuration:
  
  - `list-collections` - List all collections with blueprints and routes
  - `get-collection-entries` - Query entries with filtering and limiting
  - `get-blueprint` - Get field definitions for any blueprint
  - `list-navigations` - List navigation trees with structure
  - `list-globals` - List global sets with values
  - `list-taxonomies` - List taxonomies with term counts
  - `get-asset-containers` - List asset containers with configuration
  - `list-forms` - List forms with field definitions
  - `list-fieldtypes` - List available fieldtypes
  - `list-addons` - List installed Statamic addons
  - `stache-info` - Get Stache cache status and statistics
  - `search-statamic-docs` - Search bundled Statamic documentation
  
- **Environment Detection** - Automatically detects Statamic-only vs hybrid Laravel+Statamic apps, excluding irrelevant database tools for flat-file sites
  
- **AI Guidelines** - Includes guidance for Antlers templating, blueprints, and Statamic best practices
  
- **Install Command** - Interactive setup with environment selection
  

### Compatibility

- PHP 8.2+
- Laravel 10, 11, or 12
- Statamic 5 or 6
- Laravel Boost 1.1+
