# Changelog

All notable changes to `statamic-boost` will be documented in this file.

## Initial release of Statamic Boost - 2026-01-19

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

## v1.0.0 - 2025-01-18

### Added

- Initial release of Statamic Boost
- MCP tools for Statamic content management:
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
  - `search-statamic-docs` - Search bundled Statamic v6 documentation
  
- Environment detection for Statamic-only vs hybrid Laravel+Statamic apps
- Install command with interactive environment selection
- Automatic Inertia guideline exclusion for Statamic-only environments
- Bundled Statamic v6.0.0-beta documentation for offline search
