=== statamic/6 rules ===

## Statamic 6 Specifics

Statamic 6 runs on Laravel 12 and includes several improvements.

### New Features in v6

- **Laravel 12 support** - Built on the latest Laravel
- **Improved performance** - Faster Stache operations
- **Enhanced CP** - Updated Control Panel UI

### Breaking Changes

- `route()` method on collections now requires a site parameter: `$collection->route('default')`
- Use `routes()` to get all routes as a collection: `$collection->routes()`

### Multi-Site

Statamic 6 has improved multi-site support:

```php
// Get routes for all sites
$collection->routes(); // Returns Collection keyed by site handle

// Get route for specific site
$collection->route('default');
$collection->route('french');
```

### Control Panel

The Control Panel is available at `/cp`. Customize via:
- `config/statamic/cp.php` - CP configuration
- `resources/views/vendor/statamic/` - Override CP views

### Performance

- Enable static caching for production: `config/statamic/static_caching.php`
- Warm the Stache after deployments: `php please stache:warm`
- Use Redis for locks: `config/statamic/stache.php`
