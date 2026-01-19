### Install a Statamic Starter Kit

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This command installs a previously exported Starter Kit into a new Statamic project. You provide the Git repository path or package name of the Starter Kit you wish to use.

```php
php please starter-kit:install the-hoff/kung-fury-theme
```

--------------------------------

### Optional Module Configuration in starter-kit.yaml

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Defines optional modules that users can choose to install with the starter kit. This example shows an optional 'seo' module with its own dependencies.

```yaml
modules:
  seo:
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### Create New Statamic Site with Starter Kit (CLI)

Source: https://statamic.dev/starter-kits/installing-a-starter-kit

Initiates a new Statamic installation and installs a specified Starter Kit using a single command. This is ideal for starting a fresh project with a pre-defined structure and content. No external dependencies are required beyond the Statamic CLI.

```shell
statamic new my-site vendor/starter-kit
```

--------------------------------

### Install Starter Kit with Clear Site Option (CLI)

Source: https://statamic.dev/starter-kits/installing-a-starter-kit

Installs a Starter Kit into an existing Statamic site, forcing the removal of all existing content and configuration before installation. Use this option when you want a completely clean slate for the new Starter Kit. This is useful for migrating or starting over with a new theme or feature set.

```shell
php please starter-kit:install vendor-name/starter-kit-name --clear-site
```

--------------------------------

### Install Statamic Starter Kit with Config via CLI

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This command installs a Statamic Starter Kit into a new project while including its configuration. The `--with-config` option ensures that configuration files, such as `starter-kit.yaml`, are copied to the new project, useful for maintaining consistency or updating kits.

```shell
statamic new kung-fury-dev the-hoff/kung-fury-theme --with-config
```

--------------------------------

### Customizing Module Prompt Text

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Allows customization of the prompt text displayed to the user when an optional module is presented for installation. This example customizes the prompt for the 'seo' module.

```yaml
modules:
  seo:
    prompt: 'Would you like some awesome SEO with that!?
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### Install Statamic Starter Kit Locally via CLI

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This command demonstrates how to install a Statamic Starter Kit using a local repository path. The `--local` flag tells Composer to use the repository defined in the global `config.json`, allowing for testing of unpublished kits.

```shell
statamic new kung-fury-dev the-hoff/kung-fury-theme --local
```

--------------------------------

### Setting Default Module Selection

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Specifies a default module option to be installed when the user does not make an explicit choice or installs non-interactively. This example sets 'vue' as the default for the 'js' module selection.

```yaml
modules:
  js:
    prompt: 'Would you care for some JS?'
    default: vue
    # ... 
```

--------------------------------

### Install Dependencies and Start Vite Dev Server

Source: https://statamic.dev/extending/vite-in-addons

Commands to install project dependencies and start the Vite development server. Running 'npm run dev' enables hot reloading for rapid development.

```bash
npm install
npm run dev
```

--------------------------------

### Setting Default Value for Optional Module

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Ensures an optional module is installed by default when the starter kit is installed non-interactively or if the user presses Enter rapidly. This sets the 'seo' module to be installed by default.

```yaml
modules:
  seo:
    default: true
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### Composer Dependencies Configuration in starter-kit.yaml

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Lists Composer dependencies that should be bundled with the starter kit. The exporter automatically detects installed versions and whether they are dev dependencies.

```yaml
dependencies:
  - statamic/ssg
```

--------------------------------

### Install First-Party Statamic Addon (e.g., SSG)

Source: https://statamic.dev/addons

Installs specific first-party Statamic addons that may have dedicated Composer commands. This example shows the command for the Static Site Generator (SSG).

```shell
php please install:ssg
```

--------------------------------

### PHP Post-Install Hook for Statamic Starter Kits

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This PHP code defines a `StarterKitPostInstall` class with a `handle` method. This method is executed after a Statamic Starter Kit is installed, allowing for custom logic like outputting messages to the console. The class is non-namespaced and expects a `$console` instance as an argument.

```php
<?php

class StarterKitPostInstall
{
    public function handle($console)
    {
        $console->line('Thanks for installing!');
    }

}
```

--------------------------------

### Skipping Module Confirmation Prompt

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Configures an optional module to be installed automatically without prompting the user. Setting `prompt: false` for the 'seo' module bypasses the confirmation step.

```yaml
modules:
  seo:
    prompt: false
```

--------------------------------

### Composer Local Repository Configuration

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This JSON configuration adds a local directory as a Composer repository. It's used to test installing a Statamic Starter Kit from a local path before publishing. The `url` should point to the root of the starter kit's exported directory.

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "/Users/hasselhoff/kung-fury-theme"
        }
    ]
}
```

--------------------------------

### Selecting Between Multiple Module Options

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Allows users to choose from a list of mutually exclusive module options. This example configures a 'js' module with options for 'vue', 'react', and 'mootools', each with their own export paths.

```yaml
modules:
  js:
    options:
      vue:
        export_paths:
          - resources/js/vue.js
      react:
        export_paths:
          - resources/js/react.js
      mootools:
        export_paths:
          - resources/js/mootools.js
```

--------------------------------

### Example Default Preferences Configuration (YAML)

Source: https://statamic.dev/preferences

Illustrates the basic structure for default preferences stored in a YAML file, typically located at `resources/preferences.yaml`. This file defines application-wide settings like locale and start page.

```yaml
locale: en

start_page: collections/articles
```

--------------------------------

### Install Statamic Starter Kit

Source: https://statamic.dev/cli

Installs a Statamic starter kit package into your project. Starter kits provide pre-configured sites and structures to quickly begin development.

```php
php please starter-kit:install
```

--------------------------------

### Install Starter Kit into Existing Statamic Site (CLI)

Source: https://statamic.dev/starter-kits/installing-a-starter-kit

Installs a Starter Kit into an existing Statamic project from the project's root directory. This command allows for adding modular functionality or pre-built structures to a live site. Ensure you are in the correct directory before execution.

```shell
php please starter-kit:install vendor-name/starter-kit-name
```

--------------------------------

### Install Starter Kit Without Dependencies (CLI)

Source: https://statamic.dev/starter-kits/installing-a-starter-kit

Installs a Starter Kit into a Statamic site without including any bundled dependencies. This option is useful when you want to manually manage or already have the necessary dependencies installed. It helps in creating a leaner installation.

```shell
php please starter-kit:install vendor-name/starter-kit-name --without-dependencies
```

--------------------------------

### Full Example: MusicBrainz Artist Dictionary (PHP)

Source: https://statamic.dev/extending/dictionaries

This is a complete example of a Statamic dictionary class named `Artists`. It utilizes the `illuminate/http` and `illuminate/cache` facades to fetch artist data from the MusicBrainz API. The `options` method handles searching for artists, and the `get` method retrieves detailed artist information, caching results for performance.

```php
<?php

namespace App\Dictionaries;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Statamic\Dictionaries\Dictionary;
use Statamic\Dictionaries\Item;

class Artists extends Dictionary
{
    public function options(?string $search = null): array
    {
        if (! $search) {
            return [];
        }

        $response = Http::get('https://musicbrainz.org/ws/2/artist/?fmt=json&query=artist:'.urlencode($search))->json();

        return collect($response['artists'])->mapWithKeys(function ($artist) {
            $label = $artist['name'];

            if ($disambiguation = $artist['disambiguation'] ?? null) {
                $label .= ' ('.$disambiguation.')';
            }

            return [$artist['id'] => $label];
        })->all();
    }

    public function get(string $key): ?Item
    {
        return Cache::rememberForever('artist-'.$key, function () use ($key) {
            $response = Http::get('https://musicbrainz.org/ws/2/artist/'.$key.'?fmt=json')->json();

            return new Item($key, $response['name'], [
                'name' => $response['name'],
                'disambiguation' => $response['disambiguation'] ?? null,
                'type' => $response['type'],
                'country' => $response['country'],
            ]);
        });
    }
n}
```

--------------------------------

### Install Nginx

Source: https://statamic.dev/installing/ubuntu

Installs the Nginx web server on Debian-based systems using apt. This is a prerequisite for serving web content.

```shell
sudo apt install nginx -y
```

--------------------------------

### Automated Single to Multi-Site Conversion (PHP)

Source: https://statamic.dev/tips/converting-from-single-to-multi-site

This command automates the conversion of a Statamic single-site installation to a multi-site setup. It is the recommended method for most users.

```shell
php please multisite
```

--------------------------------

### Specifying Site Context for Global Sets

Source: https://statamic.dev/repositories/global-repository

Explains the necessity of specifying a site context when working with global sets, even in single-site setups. It provides examples using `in()`, `inDefaultSite()`, and `inCurrentSite()` methods.

```php
$set->in('siteHandle'); // Specific site handle
$set->inDefaultSite();    // First site in sites.php
$set->inCurrentSite();   // Current user's site
```

--------------------------------

### Output Content if Composer Package is Installed (Antlers & Blade)

Source: https://statamic.dev/tags/installed

This snippet illustrates using the 'installed' tag as a tag pair. Content within the pair will only be rendered if the specified Composer package is installed. Examples are provided for both Antlers and Blade.

```antlers
{{ installed:statamic/seo-pro }}

    {{ seo_pro:meta }}

{{ /installed:statamic/seo-pro }}
```

```blade
<s:installed:statamic/seo-pro>

  <s:seo_pro:meta />

</s:installed:statamic/seo-pro>
```

--------------------------------

### Module with Export Paths and Dependencies

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Configures an optional module ('seo') specifying its own export paths and Composer dependencies, similar to top-level starter kit configuration.

```yaml
modules:
  seo:
    export_paths:
      - resources/css/seo.css
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### Getting and Setting Preferences via PHP

Source: https://statamic.dev/preferences

Provides examples of how to retrieve and modify preference values using PHP. It covers fetching a preference with an optional fallback and setting default, role, or user-specific preferences, ensuring changes are saved.

```php
Preference::get($key, $fallback);
```

```php
Preference::default()->set($key, $value)->save();
$role->setPreference($key, $value)->save();
$user->setPreference($key, $value)->save();
```

--------------------------------

### GET /api/collections/{collection}/entries

Source: https://statamic.dev/content-api

Gets entries within a specified collection. Supports filtering by site for multi-site setups.

```APIDOC
## Entries

`GET` `/api/collections/{collection}/entries`

Gets entries within a collection.

### Request Example

```json
{
  "data": [
    {
      "title": "My First Day"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### Parameters

- **site** (string) - Optional - Filter results for a specific site (e.g., `&filter[site]=fr`).
```

--------------------------------

### Example Asset Sizes (HTML Output)

Source: https://statamic.dev/variables/size

This section provides an example of what the rendered HTML output might look like when displaying different asset file sizes. This is illustrative and depends on the actual files being processed.

```html
11 B

127.69 KB

1.5 MB

2 GB
```

--------------------------------

### Install Statamic Importer Addon

Source: https://statamic.dev/tips/importing-content

Installs the first-party Statamic Importer addon using Composer. This addon simplifies the process of importing entries, taxonomies, and users into Statamic with a UI for mapping data.

```bash
composer require statamic/importer
```

--------------------------------

### Initialize a Statamic Starter Kit

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This command initializes a new Statamic project to be used as a sandbox for developing a Starter Kit. It creates and wires up the necessary 'package' directory, which will form the root of your Starter Kit repository.

```php
php please starter-kit:init
```

--------------------------------

### Example Asset Container Configuration (YAML)

Source: https://statamic.dev/assets

Shows how to define an asset container's settings, including its title and the disk it uses, within a YAML file.

```yaml
# content/assets/assets.yaml

title: 'Assets'

disk: 'assets'
```

--------------------------------

### Static HTML Example

Source: https://statamic.dev/fieldtypes/users

A static HTML example demonstrating the structure for displaying user information, potentially used as a fallback or a simplified representation.

```html
<div class="bg-white p-4 shadow flex items-center">

  <img class="w-10 h-10 rounded-full" src="/img/avatars/david.jpg" alt="Avatar of David Hasselhoff">

    <div class="text-sm ml-4">

      <p class="text-gray-900 leading-none">David Hasselhoff</p>

      <p class="text-gray-600">thehoff@statamic.com</p>

    </div>

</div>
```

--------------------------------

### Install Statamic Collaboration Addon

Source: https://statamic.dev/cli

Installs the Statamic Collaboration addon and configures broadcasting in Laravel. This command is used when setting up real-time collaboration features within a Statamic project.

```php
php please install:collaboration
```

--------------------------------

### Install Statamic Eloquent Driver

Source: https://statamic.dev/cli

Installs and configures the Statamic Eloquent Driver package, which allows Statamic to use Eloquent models for content storage. This is a key command for projects utilizing Eloquent for data persistence.

```php
php please install:eloquent-driver
```

--------------------------------

### Get Asset URL using Antlers

Source: https://statamic.dev/modifiers/url

This snippet demonstrates how to get the URL of an asset using its ID in Antlers. It assumes the asset ID is stored in a variable, for example, `hero_image`. No external dependencies are required beyond Statamic.

```antlers
{{ hero_image | url }}
```

--------------------------------

### Statamic Search Form Example

Source: https://statamic.dev/search

An example of a basic HTML form for submitting search queries. The input field must be named 'q' and the form should submit to a URL containing the `search:results` tag.

```html
<form action="/search/results">

    <input type="search" name="q" placeholder="Search">

    <button type="submit">Go find it!</button>

</form>
```

--------------------------------

### Get Singular Word Form in Blade

Source: https://statamic.dev/modifiers/singular

This method uses the Statamic modifier to get the singular form of a word within Blade templates. It requires the Statamic framework to be installed and accessible. The input is expected to be a variable, and the output is the singular string.

```blade
{{ Statamic::modify($word)->singular() }}
```

--------------------------------

### Export a Statamic Starter Kit

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This command exports the developed Starter Kit from your sandbox instance to a specified directory. This exported directory is the distributable package you will version control and share on platforms like GitHub.

```php
php please starter-kit:export ../kung-fury-theme
```

--------------------------------

### Example find method in Stache Entry Repository (PHP)

Source: https://statamic.dev/tips/storing-entries-in-a-database

This PHP code shows an example of the `find` method within Statamic's default Stache Entry Repository. It demonstrates how the repository uses its query builder to find an entry by its ID.

```php
public function find($id): ?Entry
{
    return $this->query()->where('id', $id)->first();
}
```

--------------------------------

### Statamic Navigation File Structure Example

Source: https://statamic.dev/navigation

This example illustrates the directory structure for storing navigation and tree data in a Statamic project. Navigation configurations are stored in `content/navigation` and their corresponding tree structures in `content/trees`.

```yaml
content/

  navigation/

   header.yaml

   footer.yaml

  trees/

   header.yaml

   footer.yaml
```

--------------------------------

### Statamic Starter Kit Package Structure

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

A minimal Statamic Starter Kit package must include a `starter-kit.yaml` and a `composer.json` file within the `package` directory. Additional files like `README.md` can also be included.

```files
 app/
 content/
 config/
+package/ 
+  composer.json 
+  starter-kit.yaml 
 public/
 resources/
 composer.json
```

```files
 package/
   composer.json
   starter-kit.yaml
+  README.md 
```

--------------------------------

### Get All Terms of a Specific Taxonomy

Source: https://statamic.dev/content-queries/term-repository

Fetch all terms belonging to a particular taxonomy, for example, all terms with the 'tags' taxonomy.

```php
Term::query()
    ->where('taxonomy', 'tags')
    ->get();
```

--------------------------------

### Statamic Stache Entry Index Example

Source: https://statamic.dev/stache

Illustrates a basic index structure for Statamic entries, showing how entry IDs are mapped to their titles.

```txt
entry-id-1: Entry One

entry-id-2: Entry Two
```

--------------------------------

### Basic Single-Level Navigation Example

Source: https://statamic.dev/tags/nav

Provides a practical example of building a single-level navigation menu using the nav tag. It iterates through nav items, displays their titles and URLs, and adds a 'current' class to the active link. Includes 'include_home' option.

```antlers
<ul>

  {{ nav include_home="true" }}

    <li>

      <a href="{{ url }}"{{ if is_current || is_parent }} class="current"{{ /if }}>

        {{ title }}

      </a>

    </li>

  {{ /nav }}

</ul>
```

```blade
<ul>

  <s:nav include_home="true">

    <li>

      <a

        href="{{ $url }}"

        @if ($is_current || $is_parent) class="current" @endif

      >

        {{ $title }}

      </a>

    </li>

  </s:nav>

</ul>
```

--------------------------------

### Composer Configuration for Path Repository

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

To make your Starter Kit updatable, configure your project's `composer.json` to use the `package` directory as a path repository. This allows Composer to manage the Starter Kit as a local package during development.

```json
  {

      "name": "statamic/statamic",

      "require": [

 +        "the-hoff/kung-fury-theme": "dev-master" 

      ],

 +    "repositories": [

 +        {

 +            "type": "path",

 +            "url": "package"

 +        }

 +    ]

 }
```

--------------------------------

### Example Output: Collection Name

Source: https://statamic.dev/variables/collection

Demonstrates the expected HTML output when the collection tag is used, showing the 'blog' collection name.

```html
blog
```

--------------------------------

### Install AWS Flysystem Adapter

Source: https://statamic.dev/tips/digital-ocean-spaces-for-asset-container

This command installs the necessary AWS Flysystem adapter for integrating with S3-compatible services like DigitalOcean Spaces.

```shell
composer require league/flysystem-aws-s3-v3
```

--------------------------------

### Export Paths Configuration in starter-kit.yaml

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

Specifies which files and directories should be exported with the Statamic starter kit. This includes content, configuration files, blueprints, assets, and front-end build related files.

```yaml
export_paths:
  - content
  - config/filesystems.php
  - config/statamic/assets.php
  - resources/blueprints
  - resources/css/site.css
  - resources/views
  - public/assets
  - public/css
  - package.json
  - tailwind.config.js
  - webpack.mix.js
```

--------------------------------

### Example Fieldset for Import (YAML)

Source: https://statamic.dev/blueprints

An example of a Statamic fieldset definition. This fieldset contains basic fields for 'food' and 'food_reason' that can be imported into blueprints.

```yaml
# the survey.yaml fieldset

fields:
  - 
    handle: food
    type: text
  - 
    handle: food_reason
    type: textarea
```

--------------------------------

### Install Laravel Socialite for Statamic

Source: https://statamic.dev/oauth

This command installs the Laravel Socialite package, which is a dependency for Statamic's OAuth functionality. Ensure Composer is installed and accessible in your environment.

```shell
composer require laravel/socialite
```

--------------------------------

### Get Specific Site Config Value Directly

Source: https://statamic.dev/tags/get_site

This snippet demonstrates the single-tag usage of the `get_site` tag to directly retrieve a specific configuration value, such as `permalink`, for a given site handle. It shows how to achieve this concisely in both Antlers and Blade, including an example using Statamic's fluent tag syntax.

```antlers
{{ get_site:english:permalink }}
```

```blade
<s:get_site:english:permalink />
```

```blade
{{ Statamic::tag('get_site:english:permalink') }}
```

--------------------------------

### Composer JSON for Statamic Starter Kit Publishing

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This JSON configuration represents the `composer.json` file for a Statamic Starter Kit. It includes the package name and extra Statamic-specific information like the display name and description, essential for publishing and recognition.

```json
{
    "name": "the-hoff/kung-fury-theme",
    "extra": {
        "statamic": {
            "name": "Kung Fury Theme",
            "description": "Kung Fury Theme Starter Kit"
        }
    }
}
```

--------------------------------

### Use Statamic Entry Facade

Source: https://statamic.dev/repositories/entry-repository

This snippet shows how to import and use the Entry facade in PHP to interact with the Entry Repository. It's a common starting point for most entry-related operations.

```php
use Statamic\Facades\Entry;
```

--------------------------------

### Install Statamic Addon with Composer

Source: https://statamic.dev/addons

Installs a Statamic addon using Composer. This is the standard method for adding third-party functionality to your Statamic project. Ensure you have Composer installed and configured.

```shell
composer require vendor/package
```

--------------------------------

### Export Statamic Starter Kit

Source: https://statamic.dev/cli

Exports the current Statamic site as a starter kit package. This is useful for sharing your site's configuration and content structure.

```php
php please starter-kit:export
```

--------------------------------

### Update Statamic CMS Dependency

Source: https://statamic.dev/upgrade-guide/3-2-to-3-3

Modify the composer.json file to specify the new Statamic CMS version and then run composer update to install the changes and their dependencies.

```json
"statamic/cms": "3.3.*"
```

```shell
composer update statamic/cms --with-dependencies
```

--------------------------------

### Install Algolia PHP Client

Source: https://statamic.dev/search

This command installs the official Algolia search client for PHP using Composer. This dependency is required for Statamic to interact with the Algolia API. Ensure you are using a compatible version as specified.

```shell
composer require algolia/algoliasearch-client-php:^3.4
```

--------------------------------

### Local Addon Installation via Composer Path Repository

Source: https://statamic.dev/extending/addons

Configures a local addon for development by adding it as a path repository in the project's root `composer.json`. This allows Composer to install the addon directly from the local filesystem.

```json
{
    "require": {
        "acme/example": "*"
    },
    "repositories": [
        {
            "type": "path",
            "url": "addons/example"
        }
    ]
}
```

--------------------------------

### GraphQL Query Example with Multiple Root Queries

Source: https://statamic.dev/graphql

Illustrates performing multiple root-level queries simultaneously in a single GraphQL request. This example shows querying both 'entries' and 'collections' in one go.

```graphql
{

    entries {

        # ...

    }

    collections {

        # ...

    }

}
```

--------------------------------

### Footer Nav Example with Depth Check (Blade)

Source: https://statamic.dev/tips/recursive-nav-examples

Renders a footer navigation hierarchy using Statamic's Blade syntax. It mirrors the Antlers example by conditionally rendering `<h3>` and `<li>` elements based on the item's depth and recursively includes children.

```blade
<div class="flex">

  <s:nav>

    @if ($depth == 1)

      <div class="mx-10">

        <h3 class="mb-2">{{ $title }}</h3>



        @if (count($children) > 0)

          <ul>@recursive_children</ul>

        @endif

      </div>

    @elseif ($depth == 2)

      <li class="my-1">

        <a href="{{ $url }}">{{ $title }}</a>

      </li>

    @endif

  </s:nav>

</div>
```

--------------------------------

### Get Taxonomy Terms API

Source: https://statamic.dev/content-api

Retrieves all terms within a specified taxonomy. Supports filtering by site for multi-site setups. Returns a JSON object with term data, links, and metadata.

```json
{
  "data": [
    {
      "title": "Music"
    }
  ],
  "links": {},
  "meta": {}
}
```

```bash
/api/taxonomies/{taxonomy}/terms?filter[site]=fr
```

--------------------------------

### Configure Starter Kit for Updates (YAML)

Source: https://statamic.dev/starter-kits/creating-a-starter-kit

This configuration snippet demonstrates how to enable updates for a Statamic starter kit by adding `updatable: true` to the `starter-kit.yaml` file. This ensures the starter kit remains as a Composer dependency.

```yaml
+updatable: true 
export_paths: ...
```

--------------------------------

### PHP: Create and Customize a New Asset Container

Source: https://statamic.dev/repositories/asset-container-repository

This example demonstrates the process of creating a new asset container instance using the `make` method. It then shows how to chain methods to customize properties like title, download permissions, folder creation, and search indexing before saving the container.

```php
$container = AssetContainer::make('assets')
  ->title('Assets')
  ->allowDownloading(true)
  ->allowMoving(true)
  ->allowRenaming(true)
  ->allowUploads(true)
  ->createFolders(true)
  ->searchIndex('assets');

$container->save();
```

--------------------------------

### Date Field Data Structure Examples

Source: https://statamic.dev/fieldtypes/date

Illustrates how single dates, dates with times, and date ranges are stored in the data structure. Single dates are strings, while ranges are arrays with 'start' and 'end' keys.

```yaml
date: 1983-10-01

date_with_time: 1983-10-01 12:00:00

date_range:
  start: 2019-11-18
  end: 2019-11-22
```

--------------------------------

### GraphQL Query Example with Aliases

Source: https://statamic.dev/graphql

Shows how to use aliases in GraphQL to perform the same query multiple times with different parameters. This example fetches entries with IDs 'home' and 'contact', aliasing them for clarity in the response.

```graphql
{

    home: entry(id: "home") {

        title

    }

    contact: entry(id: "contact") {

        title

    }

}
```

--------------------------------

### List zip files with metadata using get_files tag

Source: https://statamic.dev/tags/get_files

This example shows how to list zip files from a web-inaccessible directory (`secure/downloads`) and display their name, size, and last modified date in a table. It utilizes the `get_files` tag to retrieve file details. This is useful for displaying downloadable files without exposing them directly via URLs.

```antlers
<table>

  <thead>

    <tr>

      <th>File</th>

      <th>Size</th>

      <th>Last Modified</th>

    </tr>

  </thead>

  <tbody>

  {{ get_files in="secure/downloads" }}

    <tr>

      <td>{{ basename }}</td>

      <td>{{ size }}</td>

      <td>{{ last_modified }}</td>

    </tr>

  {{ /get_files }}

  </tbody>

</table>
```

```blade
<table>

  <thead>

    <tr>

      <th>File</th>

      <th>Size</th>

      <th>Last Modified</th>

    </tr>

  </thead>

  <tbody>

  <s:get_files in="secure/downloads">

    <tr>

      <td>{{ $basename }}</td>

      <td>{{ $size }}</td>

      <td>{{ $last_modified }}</td>

    </tr>

  </s:get_files>

  </tbody>

</table>
```

--------------------------------

### Creating a New Taxonomy Instance

Source: https://statamic.dev/repositories/taxonomy-repository

This code demonstrates the initial step of creating a new taxonomy. It uses the `make()` method and passes the desired handle for the new taxonomy.

```php
$taxonomy = Taxonomy::make('tags');
```

--------------------------------

### Example of a Slug in HTML

Source: https://statamic.dev/variables/slug

This represents a static HTML example showing a title and its corresponding slug. It serves as a visual representation of how a slug relates to content, such as a blog post title. This is a plain HTML structure.

```html
<h1>My Thoughts on Bacon</h1>

my-thoughts-on-bacon
```

--------------------------------

### Create New Entry Instance

Source: https://statamic.dev/repositories/entry-repository

This snippet shows how to instantiate a new entry object using the `make()` method, which is the first step before creating and saving a new entry.

```php
Entry::make();
```

--------------------------------

### Get URL Segment with Blade

Source: https://statamic.dev/modifiers/segment

This example shows how to extract a URL segment using the Blade templating engine within a Statamic project. It utilizes the Statamic modifier to access the segment by its numerical index.

```blade
{{ Statamic::modify($example)->segment(4) }}
```

--------------------------------

### Get URL Segment with Antlers

Source: https://statamic.dev/modifiers/segment

This example demonstrates how to retrieve a specific segment from a URL using the Antlers templating language. It takes a URL string and a segment number as input and returns the segment at that position.

```antlers
{{ example | segment(4) }}
```

--------------------------------

### Get Item Data for Relationship Fieldtype (PHP)

Source: https://statamic.dev/extending/relationship-fieldtypes

PHP method to retrieve detailed data for selected items in the relationship field. This example looks up tweet details from an external API using provided IDs.

```php
public function getItemData($values, $site = null)
{
    $tweets = Twitter::getStatusesLookup(['id' => implode(',', $values)]);

    return $this->formatTweets($tweets);
}
```

--------------------------------

### Create a New Statamic Entry Instance

Source: https://statamic.dev/repositories/entry-repository

Initiate the creation of a new Statamic entry. You must provide at least the collection handle and a unique slug for the entry. This is the first step before setting other entry attributes.

```php
$entry = Entry::make()->collection('blog')->slug('my-entry');
```

--------------------------------

### Example HTML Output for Taxonomy Entries

Source: https://statamic.dev/tags/taxonomy

An example of the generated HTML output when displaying taxonomy terms and their associated entries, showing structured lists for different categories like 'News' and 'Events'.

```html
<h2>News</h2>
<ul>
  <li><a href="/blog/breaking">A breaking story!</a></li>
  <li><a href="/blog/so-interesting">An interesting article</a></li>
</ul>

<h2>Events</h2>
<ul>
  <li><a href="/events/walk-in-the-park">A walk in the park</a></li>
  <li><a href="/events/summer-camp">Summer camp</a></li>
</ul>
```

--------------------------------

### Example of Statamic API Filtering with Multiple Conditions

Source: https://statamic.dev/content-api

Provides an example of using multiple `filter` query parameters to refine API results. This example filters by 'title' containing 'awesome' and 'featured' being true.

```url
/endpoint?filter[title:contains]=awesome&filter[featured]=true
```

--------------------------------

### Creating a New AssetContainer

Source: https://statamic.dev/repositories/asset-container-repository

Guides through the process of creating a new asset container instance, configuring its properties, and saving it.

```APIDOC
## Creating a New AssetContainer

### Description
Steps to create and configure a new asset container.

### Method
`make()`

### Endpoint
N/A (This is a repository method, not an HTTP endpoint)

### Parameters

#### Request Body (Implicit through chained methods)

- **`handle`** (string) - Required - The unique handle for the asset container.
- **`title`** (string) - Optional - The display title for the asset container.
- **`allowDownloading`** (boolean) - Optional - Whether downloads are allowed.
- **`allowMoving`** (boolean) - Optional - Whether moving assets is allowed.
- **`allowRenaming`** (boolean) - Optional - Whether renaming assets is allowed.
- **`allowUploads`** (boolean) - Optional - Whether uploads are allowed.
- **`createFolders`** (boolean) - Optional - Whether folder creation is allowed.
- **`searchIndex`** (string) - Optional - The search index to use for assets in this container.

### Request Example
```php
$container = AssetContainer::make('my_new_container')
  ->title('My New Assets')
  ->allowDownloading(true)
  ->allowMoving(true)
  ->allowRenaming(true)
  ->allowUploads(true)
  ->createFolders(true)
  ->searchIndex('assets');

$container->save();
```

### Response
#### Success Response (200)
Indicates the asset container was successfully created and saved.

#### Response Example
```json
{
  "message": "Asset container 'my_new_container' created successfully."
}
```
```

--------------------------------

### Build a Site Switcher in Antlers

Source: https://statamic.dev/multi-site

Create a navigation component that allows users to switch between different sites. This example loops through available sites, highlighting the current one and providing links.

```antlers
{{ sites }}
  <a class="{{ site:handle === handle ?= 'active' }}" href="{{ url }}">
    {{ handle }}
  </a>
{{ /sites }}
```

--------------------------------

### Verify Statamic CMS Path via Composer

Source: https://statamic.dev/contribution-guide

This command queries Composer to display information about the installed `statamic/cms` package, including its installation path. This is useful for verifying that Composer has correctly symlinked or installed the package from the specified local path repository.

```shell
composer show statamic/cms --path
```

--------------------------------

### Getting All Taxonomies and Finding by ID/Handle

Source: https://statamic.dev/repositories/taxonomy-repository

These examples show how to retrieve all available taxonomies using the `all()` method or find a specific taxonomy by its ID or handle using `find()` or `findByHandle()`. If a taxonomy is not found with `find()`, it returns null.

```php
$allTaxonomies = Taxonomy::all();

$taxonomyById = Taxonomy::find('your-taxonomy-id');
$taxonomyByHandle = Taxonomy::findByHandle('your-taxonomy-handle');
```

--------------------------------

### Get Index Items for Relationship Fieldtype (PHP)

Source: https://statamic.dev/extending/relationship-fieldtypes

PHP method to fetch and format items for the relationship field's selector. This example retrieves tweets from a user's timeline via an external API and formats them.

```php
use Carbon\Carbon;

public function getIndexItems($request)
{
    $tweets = Twitter::getUserTimeline([
        'screen_name' => $this->config('screen_name')
    ]);

    return $this->formatTweets($tweets);
}

protected function formatTweets($tweets)
{
    return collect($tweets)->map(function ($tweet) {
        $date = Carbon::parse($tweet->created_at);

        return [
            'id'            => $tweet->id_str,
            'text'          => $tweet->text,
            'date'          => $date->timestamp,
            'date_relative' => $date->diffForHumans(),
            'user'          => $tweet->user->screen_name,
        ];
    });
}
```

--------------------------------

### GraphQL Response Example for Filtered Entries

Source: https://statamic.dev/graphql

An example JSON response from a GraphQL query that has been filtered. This illustrates the expected output structure when filtering 'entries' based on specific criteria.

```json
{

    "data": [

        { "title": "That was so rad!" },

        { "title": "I wish I was as cool as Daniel Radcliffe!" },

    ]

}
```

--------------------------------

### Get URL Segment in Antlers

Source: https://statamic.dev/variables/segment_x

Retrieves the value of a specific URL segment using the `segment` variable within Antlers templates. For example, `segment_3` will return the third segment of the URL. Ensure the segment exists to avoid errors.

```antlers
{{ segment_3 }}
```

--------------------------------

### List Support Details

Source: https://statamic.dev/cli

Displays useful details about the current Statamic installation and environment that can aid in support requests. This information helps developers and support staff diagnose issues more effectively.

```php
php please support:details
```

--------------------------------

### Basic Antlers View Example

Source: https://statamic.dev/views

A simple HTML structure demonstrating a basic Statamic view file. This view is intended for direct rendering of content.

```antlers
<html>
  <body>
    <p>The invention of the shovel was ground breaking.</p>
  </body>
</html>
```

--------------------------------

### Include SEO Pro URLs in config/statamic/ssg.php

Source: https://statamic.dev/deploying/netlify

To ensure `sitemap.xml` and `humans.txt` are generated by the SEO Pro addon, add their respective URLs to the 'urls' array in the `config/statamic/ssg.php` configuration file. This makes them part of your statically generated site.

```php
# config/statamic/ssg.php

'urls' => [

    '/sitemap.xml',

    '/humans.txt',

],
```

--------------------------------

### Query Entry by Collection, Slug, and Site

Source: https://statamic.dev/repositories/entry-repository

This snippet illustrates querying for an entry using its collection handle, slug, and a specific site handle in a multi-site Statamic installation.

```php
Entry::query()
    ->where('collection', 'team')
    ->where('slug', 'director')
    ->where('site', 'albuquerque')
    ->get();
```

--------------------------------

### Get Index Query for Relationship Fieldtype (PHP)

Source: https://statamic.dev/extending/relationship-fieldtypes

PHP method to define the query for retrieving items in the relationship field's index. This example retrieves entries from specified collections using Statamic's Entry API.

```php
public function getIndexQuery($request)
{
    return Entry::query()->whereIn('collection', $request->collections);
}
```

--------------------------------

### Test Nginx Configuration

Source: https://statamic.dev/installing/ubuntu

Checks the Nginx configuration files for syntax errors before applying changes. This command helps prevent Nginx from failing to start due to configuration mistakes.

```shell
sudo nginx -t
```

--------------------------------

### PHP Entries Fieldtype Query Builder Change

Source: https://statamic.dev/upgrade-guide/3-2-to-3-3

Illustrates the change in PHP when using the entries fieldtype, which now augments to a query builder. A ->get() method must be appended to retrieve the entries before applying collection methods.

```php
$relatedPosts->someCollectionMethod(...);
```

```php
$relatedPosts->get()->someCollectionMethod(...);
```

--------------------------------

### Composer Script for Building Statamic Site

Source: https://statamic.dev/deploying/netlify

This script defines the build process for a Statamic site when deploying to platforms like Netlify. It includes steps for building frontend assets with npm, copying environment files, generating an application key, and finally generating the static site using Statamic's SSG command.

```json
{
    "scripts": {
        "build": [
            "npm run build",
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
            "@php artisan key:generate",
            "@php please ssg:generate"
        ]
        // ...
    }
}
```

--------------------------------

### Example Asset Metadata (YAML)

Source: https://statamic.dev/assets

Demonstrates the structure of a YAML file used for storing cached asset metadata, including file size, dimensions, and custom user data defined by blueprints.

```yaml
size: 9151

last_modified: 1558533973

width: 216

height: 104

data:

  alt: 'A tree with a tire swing'

  focus: 54-54-1
```

--------------------------------

### Create Statamic Entry Instance with Data

Source: https://statamic.dev/extending/data

Demonstrates creating a new Statamic entry instance, setting its published status and data, and then saving it. It emphasizes using the `make` method for correct class instantiation.

```php
use Statamic\Facades\Entry;


$entry = Entry::make()

            ->published(true)

            ->data(['title' => 'About us', 'subtitle' => 'We are awesome'])

            ->etc(); // and so on...


$entry->save();
```

--------------------------------

### Query All Localized Terms in PHP

Source: https://statamic.dev/upgrade-guide/3-3-to-3-4

Demonstrates how to retrieve all localizations of terms using Term::all() or Term::query()->get() in PHP. This behavior changed in Statamic 3.4 to return all localizations instead of deduplicated terms.

```php
Term::all();

Term::query()->get();
```

--------------------------------

### Antlers Delimiter Formatting Examples

Source: https://statamic.dev/antlers

Illustrates various formatting styles for Antlers expressions within delimiters. It covers recommended practices for readability and shows examples of both valid and invalid formatting.

```antlers
{{ perfectenschlag }}

{{squished}}

{{ 
  testimonials

  limit="5"

  order="username"
}}

{{playSad_Tromb0ne            }}
```

--------------------------------

### Use Blog Listing Partial with Slot (Antlers)

Source: https://statamic.dev/quick-start-guide

This example shows how to use the reusable blog listing partial in a home template. It demonstrates passing a `limit` argument and using the partial as a tag pair to insert content into the partial's slot. This promotes code reuse and cleaner templates.

```antlers
// resources/views/home.antlers.html



<h1 class="text-2xl font-bold my-6">Welcome to my CyberSpace Place!</h1>

{{ content }}

{{ partial:blog/listing limit="5" }}

    <h2 class="p-5">Recent Blog Posts</h2>

{{ /partial:blog/listing }}
```

--------------------------------

### Get Single Parent Variable (Blade)

Source: https://statamic.dev/tags/parent

Fetches a specific variable from the parent entry using Blade syntax. This includes examples using Antlers Blade Components and Fluent Tags for accessing parent data like 'title'.

```blade
{{-- Using Antlers Blade Components --}}
<s:parent:title />


{{-- Using Fluent Tags --}}
{{ Statamic::tag('parent:title') }}
```

--------------------------------

### Example Absolute URL Output (HTML)

Source: https://statamic.dev/variables/permalink

This is an example of the HTML output generated when using the `permalink` tag. It represents a complete URL pointing to a specific piece of content on the website.

```html
http://example.com/posts/bacon
```

--------------------------------

### Dynamically Pass Site Handle

Source: https://statamic.dev/tags/get_site

This example shows how to dynamically pass a site handle to the `get_site` tag in both Antlers and Blade. This allows you to fetch configuration for a site whose handle is determined at runtime, for instance, from a variable or parameter.

```antlers
{{ get_site :handle="another_sites_handle" }}

    <!-- ... -->

{{ /get_site }}
```

```blade
<s:get_site :handle="$another_sites_handle">

  <!-- ... -->

</s:get_site>
```

--------------------------------

### Example Usage: Cookie

Source: https://statamic.dev/variables/segment_x

This snippet demonstrates retrieving the 'cookie' segment from a URL. While not a direct code example of `segment_x`, it illustrates the expected output when a segment matches 'cookie'.

```html
cookie
```

--------------------------------

### Create a New User Group Instance

Source: https://statamic.dev/repositories/user-group-repository

Demonstrates how to create a new user group instance using the `make()` method, optionally passing the group's handle. Further customization can be done by chaining methods before saving.

```php
$group = UserGroup::make('admin');
```

```php
$group
    ->title('Administrators')
    ->roles($roles); // array of role handles
```

```php
$group->save();
```

--------------------------------

### Check if Date is Between Two Dates (Antlers, Blade)

Source: https://statamic.dev/modifiers/is_between

The 'is_between' modifier checks if a given date falls between a start and end date. It accepts variable names or literal date strings. This example shows its usage in both Antlers and Blade templating.

```antlers
{{ if date | is_between($start_date, $end_date) }}
```

```blade
@if (Statamic::modify($date)->isBetween([$start_date, $end_date])->fetch()) @endif
```

--------------------------------

### Query Entries by Collection Handle

Source: https://statamic.dev/repositories/entry-repository

This example demonstrates how to query and retrieve all entries belonging to a specific collection, identified by its handle (e.g., 'blog').

```php
Entry::query()
  ->where('collection', 'blog')
  ->get();
```

--------------------------------

### List non-asset images using get_files tag

Source: https://statamic.dev/tags/get_files

This example demonstrates how to use the `get_files` tag to list image files within a specified directory (`public/img/brand`). It iterates through the found files and displays them as `<img>` elements. It's designed for non-asset images.

```antlers
{{ get_files in="public/img/brand" }}

  <img src="{{ file }}" class="w-1/3">

{{ /get_files }}
```

```blade
<s:get_files in="public/img/brand">

  <img src="{{ $file }}" class="w-1/3">

</s:get_files>
```

--------------------------------

### Creating an Asset Instance with Path

Source: https://statamic.dev/repositories/asset-repository

This snippet demonstrates how to create a new Asset instance using the `make` method, specifying the container and the asset's path. This is the first step before saving or uploading an asset.

```php
$asset = Asset::make()->container('assets')->path('images/hat.jpg');
```

--------------------------------

### Example JSON Response for Single Select/Radio/Checkbox/ButtonGroup

Source: https://statamic.dev/graphql

Provides an example of the JSON response structure for a single-value selection field (like a single select or radio button) in Statamic.

```json

"my_single_select_field": {

    "value": "potato",

    "label": "Potato"

}

```

--------------------------------

### Retrieving Global Sets

Source: https://statamic.dev/repositories/global-repository

Demonstrates methods for retrieving global sets. `all()` gets all sets, `find()` and `findOrFail()` retrieve by ID, and `findByHandle()` gets a set by its unique handle. `make()` creates a new instance.

```php
$allSets = GlobalSet::all();
$setById = GlobalSet::find('some-id');
$setByHandle = GlobalSet::findByHandle('theme');
$setOrFail = GlobalSet::findOrFail('another-id');
$newSet = GlobalSet::make();
```

--------------------------------

### Creating a New Role Instance

Source: https://statamic.dev/repositories/user-role-repository

This code shows how to create a new Role instance using the `make` method of the Role Facade, optionally passing a handle.

```php
$role = Role::make('editors');
```

--------------------------------

### Substr Modifier Usage in Antlers

Source: https://statamic.dev/modifiers/substr

Demonstrates how to use the substr modifier in Antlers to extract parts of a string. It shows examples of getting the first three characters, a middle segment, and a segment from the end of the string. No external dependencies are required beyond the Statamic framework.

```antlers
{{ string | substr(0, 3) }}

{{ string | substr(4, 4) }}

{{ string | substr(-8, 8) }}
```

--------------------------------

### Get All Entries with Entry Repository

Source: https://statamic.dev/repositories/entry-repository

Retrieves all entries using the `all()` method from the Entry Repository. This is a simple way to fetch all available entries in the system.

```php
Entry::all();
```

--------------------------------

### Process Entries and Get IDs with Fetched-Entries Hook (PHP)

Source: https://statamic.dev/extending/hooks

This example shows how to use the 'fetched-entries' hook to first pass the payload to the next closure, then process the modified entries to extract their IDs. This is useful when you need to perform actions after other hooks have potentially altered the data.

```php
use Statamic\Tags\Collection;

Collection::addHook('fetched-entries', function ($entries, $next) {
    // Pass the payload along to the next registered closures.
    $entries = $next($entries);

    $ids = $entries->pluck('id');

    // You'll still need to return it!
    return $entries;
});
```

--------------------------------

### Sidebar Nav Example with Dynamic Classes (Antlers)

Source: https://statamic.dev/tips/recursive-nav-examples

Renders a sidebar navigation as a `<ul>` with dynamic CSS classes applied based on the page's depth. A mapping `nav_classes` is used to assign specific styles for different navigation levels.

```antlers
---

nav_classes:

  1: 'text-gray-900 font-bold'

  2: 'text-gray-800 ml-3'

  3: 'text-gray-500 ml-6 text-sm'

---



<ul class="nav">

    {{ nav }}

        <li>

            <span class="{{ view:nav_classes[depth] }}">

                {{ title }}

            </span>

            {{ if children }}

                <ul class="{{ depth == 1 ?= 'mb-4' }}">

                    {{ *recursive children* }}

                </ul>

            {{ /if }}

        </li>

    {{ /nav }}

</ul>
```