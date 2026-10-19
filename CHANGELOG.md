# Changelog

All notable changes to `statamic-boost` will be documented in this file.

## v1.4.0 - 2026-10-19

Refresh the bundled Statamic documentation from pinned upstream snapshots.

- Statamic 5: 583 pages from [c3b2919e8ba6](https://github.com/statamic/docs/commit/c3b2919e8ba6d48e2792c8b2276cc4e42fe78af6).
- Statamic 6: 614 pages from [9e26cc421778](https://github.com/statamic/docs/commit/9e26cc4217784b0bf135965d0c33a03159d3a852).

Documentation remains available offline through the existing search tool. No new Statamic major is enabled by this release.

## v1.3.0 - 2026-10-10

### Fixed

- Allow current Laravel Boost 2 installations with Laravel MCP `^1.0.1`.
- Preserve the selected Statamic environment when Boost rebuilds its configuration, and return installer failures correctly.
- Fix fieldtype discovery and Antlers examples in generated AI guidelines.

### Added

- Fresh-install CI for PHP 8.5, Laravel 13, Statamic 6 and Boost 2, covering installation, reinstallation, generated configuration and runtime tools.
- Refreshed offline Statamic 5 and 6 documentation, with pinned source links and upstream licenses.
- Weekly documentation updates that publish minor releases when docs change, plus one tracking issue per newly detected major.
- Read-only documentation dry runs on pull requests.

This release establishes the baseline for automatic documentation releases.

**Full changelog:** https://github.com/chrisvasey/statamic-boost/compare/v1.2.1...v1.3.0

## v1.2.1 - 2026-03-26

### Fixed

- **laravel/mcp compatibility**: Updated `laravel/mcp` requirement from `^0.5` to `^0.5|^0.6`, unblocking installation for projects on `laravel/mcp` v0.6.x. Closes #14.

### Maintenance

- CI matrix now tests against `laravel/mcp` ^0.5 and ^0.6, Laravel 11/12/13, and Pest 3/4.

## v1.2.0 - 2026-03-22

### What's Changed

#### New

- **Guidelines auto-discovery**: Statamic guidelines are now automatically included when running `php artisan boost:install` — no manual publishing required. The package now ships `resources/boost/guidelines/core.blade.php` which Laravel Boost v2 picks up automatically during install. (#19)

#### Fixed

- **Laravel Boost v2 compatibility**: Updated `laravel/boost` requirement from `^1.1` to `^2.0`, resolving composer conflicts for projects on Laravel 12 / Boost v2.x. Closes #14. (#19)
- **Statamic 6.7+ compatibility**: `GetAssetContainers` tool now catches `\Throwable` instead of `\Exception`, fixing a crash caused by a breaking change in Statamic 6.7's augmentation layer. (#19)

#### Maintenance

- Bumped `ramsey/composer-install` GitHub Action from v3 to v4 (#17)

**Full Changelog**: https://github.com/chrisvasey/statamic-boost/compare/v1.1.2...v1.2.0

## Unreleased

- Add `resources/boost/guidelines/core.blade.php` so guidelines are auto-discovered by `boost:install` (#10)
- Update `laravel/boost` requirement from `^1.1` to `^2.0` (#14)

## v1.1.2 - 2026-01-21

- Remove `/docs` from `export-ignore` in `.gitattributes` so documentation files are included when the package is installed via Composer
- Fixes the "Documentation file not found" error in the `search-statamic-docs` MCP tool

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
