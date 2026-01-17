=== statamic/core rules ===

## Statamic CMS

Statamic is a flat-file CMS built on Laravel. Content is stored as YAML/Markdown files in the `content/` directory, making it Git-friendly.

### Project Structure
- `content/collections/` - Content entries organized by collection
- `content/globals/` - Site-wide variables
- `content/navigation/` - Navigation structures
- `content/taxonomies/` - Categories, tags, etc.
- `content/assets/` - Asset container metadata
- `resources/blueprints/` - Field definitions for content types
- `resources/views/` - Antlers templates (`.antlers.html`)

### Antlers Templating

Antlers is Statamic's templating language. Use double curly braces for variables and tags.

**Variables:**
```antlers
@{{ title }}
@{{ content | markdown }}
@{{ date | format('F j, Y') }}
```

**Tags (with parameters):**
```antlers
@{{ collection:blog limit="5" }}
    <article>
        <h2>@{{ title }}</h2>
        @{{ content }}
    </article>
@{{ /collection:blog }}
```

**Conditionals:**
```antlers
@{{ if logged_in }}
    Welcome, @{{ current_user:name }}
@{{ elseif show_login }}
    <a href="/login">Login</a>
@{{ else }}
    Guest content
@{{ /if }}
```

**Modifiers (pipe syntax):**
```antlers
@{{ title | upper }}
@{{ content | markdown | safe }}
@{{ date | relative }}
@{{ image | glide:width="800" }}
```

### Blueprints

Blueprints define the content structure. They use YAML format:

```yaml
title: Article
tabs:
  main:
    display: Main
    sections:
      -
        fields:
          -
            handle: title
            field:
              type: text
              required: true
          -
            handle: content
            field:
              type: markdown
          -
            handle: author
            field:
              type: users
              max_items: 1
```

### Collections

Collections group related content. Configuration in `content/collections/{handle}.yaml`:

```yaml
title: Blog
route: '/blog/{slug}'
dated: true
sort_dir: desc
template: blog/show
layout: layout
blueprints:
  - article
taxonomies:
  - tags
  - categories
```

### Common Fieldtypes
- `text`, `textarea`, `markdown` - Text content
- `assets` - File/image uploads
- `entries` - Relationships to other entries
- `users` - User relationships
- `terms` - Taxonomy term relationships
- `bard` - Rich text editor with blocks
- `replicator` - Repeating field sets
- `grid` - Tabular data
- `toggle` - Boolean values
- `select`, `radio`, `checkboxes` - Option fields

### Artisan Commands

Use `php please` (or `php artisan`) for Statamic commands:

```bash
# Content
php please make:collection {name}
php please make:taxonomy {name}
php please make:global {name}
php please make:nav {name}

# Users
php please make:user

# Cache
php please stache:refresh
php please stache:warm
php please cache:clear

# Static Caching
php please static:clear
php please static:warm
```

### Routing

Statamic routes entries automatically based on collection configuration. Custom routes go in `routes/web.php`:

```php
use Statamic\Facades\Entry;

Route::statamic('search', 'search');
Route::statamic('api/posts', 'api.posts', ['layout' => false]);
```

### Querying Content

Use Statamic's fluent query builders:

```php
use Statamic\Facades\Entry;
use Statamic\Facades\Term;
use Statamic\Facades\GlobalSet;

// Query entries
Entry::query()
    ->where('collection', 'blog')
    ->where('published', true)
    ->orderBy('date', 'desc')
    ->limit(10)
    ->get();

// Get a specific entry
Entry::find('blog::my-post');
Entry::findByUri('/blog/my-post');

// Globals
GlobalSet::findByHandle('site')->in('default')->get('company_name');
```

### Tags Reference

Common Antlers tags:
- `@{{ collection:* }}` - Query collection entries
- `@{{ taxonomy:* }}` - Query taxonomy terms
- `@{{ nav:* }}` - Render navigation
- `@{{ form:* }}` - Render forms
- `@{{ user:* }}` - Current user data
- `@{{ asset:* }}` - Asset information
- `@{{ glide }}` - Image manipulation
- `@{{ partial:* }}` - Include partials
- `@{{ cache }}` - Cache blocks
- `@{{ session:* }}` - Session data
- `@{{ route }}` - Generate URLs

### Best Practices

1. **Use blueprints** - Define all fields in blueprints, not inline
2. **Leverage taxonomies** - For categories, tags, and relationships
3. **Partial templates** - Break templates into reusable partials
4. **Use Glide** - For responsive images: `@{{ image | glide:width="800" }}`
5. **Static caching** - Enable for production sites
6. **Git-friendly** - Commit content files for version control
