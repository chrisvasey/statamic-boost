# Statamic Boost

Statamic-specific MCP tools and AI guidelines for [Laravel Boost](https://github.com/laravel/boost). Provides AI assistants with deep knowledge of your Statamic site's structure, content, and configuration.

## Features

- **12 MCP Tools** for querying Statamic content, blueprints, and configuration
- **AI Guidelines** for Antlers templating, blueprints, and Statamic best practices
- **Environment Detection** automatically excludes database tools for flat-file sites
- **Seamless Integration** with Laravel Boost's MCP server

This package focuses on providing **read-only context** to AI agents about your Statamic site. If you need full CRUD operations, template analysis, or addon scaffolding, check out [cboxdk/statamic-mcp](https://github.com/cboxdk/statamic-mcp) which offers 100+ tools with write capabilities.

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- Statamic 5 or 6
- [Laravel Boost](https://github.com/laravel/boost) ^2.0

## Installation

```bash
composer require chrisvasey/statamic-boost --dev
```

Then run the Boost installer to generate your AI context files:

```bash
php artisan boost:install
```

This is **Laravel Boost's** install command. It auto-discovers Statamic Boost's guidelines and includes them in your generated CLAUDE.md / agent context files.

> **Note:** There is also a `php artisan statamic-boost:install` command (or `php please statamic-boost:install`). This is a separate command that configures your environment type (Statamic-only, hybrid, or Laravel-only) and then calls `boost:install` for you. Run it instead of `boost:install` if you want to be prompted for your environment type.

To install the MCP into your preferred editor/cli, follow the instructions on the [Laravel Boost Install Guide](https://laravel.com/ai/boost#installation-guide).

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
php please vendor:publish --tag=statamic-boost-config
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

Guidelines are automatically included when you run `php please boost:install`.

## Bundled Documentation Updates

Documentation search runs offline against snapshots of the official `statamic/docs`
`5.x` and `6.x` branches. The bundles preserve Markdown and reference information
stored in YAML, including tag parameters and fieldtype options. Each result links
to its exact upstream source commit. `docs/upstream.json` records the source commits,
page counts and content checksums; the upstream licenses are included in `docs/`.

The **Update bundled documentation** action checks every Monday at 05:23 UTC.
It downloads immutable upstream snapshots, validates both versions, and publishes
the next minor release only when bundled documentation or its license changes.
Commit-only changes and non-documentation site edits do not cause releases.
The action runs the tests and static analysis before atomically pushing the docs
commit and tag, then creates a GitHub release and includes the changelog update.

New numbered major branches (for example `7.x`) create one tracking issue per major.
An existing issue, including a closed one, prevents duplicates. New majors do not automatically
change Composer compatibility or replace an existing version's documentation.
Checks for new majors continue even when a docs release is deferred.

Automatic releases require an existing stable GitHub release and no unrelated
changes since that release. **Publish a normal release containing this workflow
and the pending package fixes before expecting automatic docs releases.** The
workflow reports a deferred release when that prerequisite is not met. Once the
baseline exists, it needs only the repository's `GITHUB_TOKEN` with `contents: write`
and `issues: write`; no separate access token is required. Branch protection, if
enabled later, must permit the intended docs updates.

Use **Actions → Update bundled documentation → Run workflow** on `main` to check
manually. Dry run is enabled by default: it validates and reports changes without
creating issues, commits, tags or releases. It rebuilds both upstream snapshots
even when their commits are unchanged or the normal-release prerequisite is unmet.
Relevant pull requests run the same dry run with read-only repository permissions,
followed by the package tests and static analysis. Dry-run plans cannot be published.
Uncheck dry run to publish after the release prerequisites are met. Existing tags
are never overwritten. If a release API call fails after the commit and tag were
pushed, rerunning completes that release while the tagged commit remains at `main`.

To regenerate one bundle locally from an upstream snapshot:

```bash
php scripts/bundle-docs.php /path/to/statamic-docs 6 FULL_COMMIT_SHA docs/statamic-docs-v6.md
node --test scripts/tests/docs-release.test.mjs
composer test
```

The action also updates the manifest and licenses. Local bundle changes must keep
those files consistent. Unsupported Statamic majors return a documentation error
instead of silently searching another major's bundle.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
