### Create New Statamic Site

Source: https://v6.statamic.dev/quick-start-guide

Creates a new Statamic website project using the installed CLI. It involves navigating to the desired directory and running the 'statamic new' command, followed by configuration prompts for the site.

```shell
cd ~/Sites && statamic new cyberspace-place
```

--------------------------------

### Create a Statamic Sandbox Project

Source: https://v6.statamic.dev/contribution-guide

This command creates a new Statamic project using the Starter Kit in a specified directory. This is necessary because the `cms` repository is a Laravel package and requires a full Laravel application to run. The command initiates the project setup and installs Statamic.

```shell
cd sites 
statamic new sandbox 
Creating a statamic/statamic project at ./sandbox

[✔] Statamic has been successfully installed into the sandbox directory.

Build something rad!
```

--------------------------------

### Configuring Optional Modules in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Sets up optional modules within a Starter Kit. These modules can have their own export paths and dependencies, and users can choose whether to install them during the Starter Kit setup.

```yaml
modules:
  seo:
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### Install Statamic

Source: https://v6.statamic.dev/advanced-topics/cli

Installs Statamic into the current project. This command is typically run during the initial setup of a new Statamic site.

```shell
php please install
```

--------------------------------

### Create New User

Source: https://v6.statamic.dev/quick-start-guide

Generates a new user account for the Statamic site via the command line. It prompts for user details and allows setting administrative privileges, which is crucial for accessing the control panel.

```shell
php please make:user
```

--------------------------------

### Blog Index Page (Antlers)

Source: https://v6.statamic.dev/quick-start-guide

This snippet creates a blog index page listing all blog posts. It fetches collections of blog posts and displays their title and date. It is designed to be refactored using partials.

```antlers
<h1 class="text-2xl font-bold my-6">{{ title }}</h1>

{{ content }}

<section class="border border-green-400 mt-12">
{{ collection:blog }}
    <a href="{{ url }}" class="flex items-center justify-between p-5 border-t border-green-400 text-green-400 hover:text-green-900 hover:bg-green-400">
        <span>{{ title }}</span>
        <span class="text-green-900 text-sm">{{ date }}</span>
    </a>
{{ /collection:blog }}
</section>
```

--------------------------------

### Serve Statamic Site with Built-in Server

Source: https://v6.statamic.dev/getting-started/installing/local

Starts the built-in PHP development server for your Statamic project. This command is useful if you don't have a local development environment like Laravel Valet set up.

```cli
php artisan serve
```

--------------------------------

### Test Nginx Configuration

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Tests the Nginx configuration files for syntax errors before applying them. This command helps prevent Nginx from failing to start due to a misconfiguration.

```shell
sudo nginx -t
```

--------------------------------

### Blog Listing Partial with Limit (Antlers)

Source: https://v6.statamic.dev/quick-start-guide

This is a reusable partial for displaying blog posts, allowing a 'limit' variable to control the number of posts shown. It's intended to be included in other templates and supports slots for additional content.

```antlers
<section class="border border-green-400 mt-12">
    {{ slot }}
    {{ collection:blog :limit="limit" }}
...

```

--------------------------------

### Install Statamic CLI using Composer

Source: https://v6.statamic.dev/getting-started/installing/laravel-herd

Installs the Statamic Command Line Interface globally using Composer. This allows you to create new Statamic sites with a guided setup wizard.

```shell
composer global require statamic/cli
```

--------------------------------

### Example Asset Publish Command

Source: https://v6.statamic.dev/addons/building-an-addon

This command is automatically executed by `statamic:install` to publish your addon's assets. It uses a tag derived from your addon's slug to identify and copy the assets.

```shell
php artisan vendor:publish --tag=your-addon-slug --force
```

--------------------------------

### Display Blog Entry (Antlers)

Source: https://v6.statamic.dev/quick-start-guide

This snippet displays a single blog post. It automatically fetches entry data when on a unique entry URL. It accesses fields like 'title', 'date', and 'author.name', and renders 'content' which is auto-converted from Markdown to HTML.

```antlers
<h1 class="text-3xl bg-green-400 text-center text-green-900 font-bold mt-6 p-6">{{ title }}</h1>

<div class="border text-center text-green-600 border-green-400 mt-8 p-3 text-xs uppercase">
    Published on {{ date }} by {{ author:name }}
</div>

<article class="space-y-4 mt-8 text-sm text-green-400 leading-loose">
    {{ content }}
</article>
```

--------------------------------

### Setting Default Module Selection in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Specifies a default module to be selected when a user is presented with multiple options. This ensures a preferred module is pre-chosen, streamlining the installation process for non-interactive or quick setups.

```yaml
modules:
  js:
    prompt: 'Would you care for some JS?'
    default: vue
    # ...
```

--------------------------------

### Development Commands for Vite

Source: https://v6.statamic.dev/addons/vite-tooling

Commands to install npm dependencies and start the Vite development server for hot reloading.

```bash
npm install

npm run dev
```

--------------------------------

### Install a Statamic Starter Kit

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Installs a starter kit into a new or existing Statamic project using its package name. This command fetches and applies the starter kit's files and configurations.

```php
php please starter-kit:install the-hoff/kung-fury-theme
```

--------------------------------

### Setup Vite for Control Panel (Bash)

Source: https://v6.statamic.dev/control-panel/css-javascript

Command to set up Vite for the Statamic Control Panel. It installs dependencies, creates configuration files, and publishes stubs.

```bash
php please setup-cp-vite
```

--------------------------------

### Manage Addon Settings using Addon Facade

Source: https://v6.statamic.dev/addons/building-an-addon

Demonstrates how to retrieve and set addon settings using the Addon facade. It includes examples for getting individual settings, all settings, raw settings, and setting single or multiple values before saving.

```php
use Statamic\Facades\Addon;

$addon = Addon::get('vendor/package');

// Getting settings...
$addon->settings()->get('api_key');

$addon->settings()->all();
$addon->settings()->raw(); // Doesn't evaluate Antlers

// Setting values...
$addon->settings()->set('api_key', '{{ config:services:example:api_key }}');

$addon->settings()->set([
    'website_name' => 'My Awesome Site',
    'api_key' => '{{ config:services:example:api_key }}',
]);

// Saving...
$addon->settings()->save();
```

--------------------------------

### Install Statamic Starter Kit

Source: https://v6.statamic.dev/advanced-topics/cli

Installs a starter kit package into the current Statamic project. This command applies the pre-defined structure and content from a starter kit.

```shell
php please starter-kit:install
```

--------------------------------

### Get Entry by URI using Statamic

Source: https://v6.statamic.dev/repositories/entry-repository

Illustrates how to fetch an entry based on its URI using the Statamic Entry Facade. It includes examples using the query builder and the convenient `findByUri()` method. The difference between URI and URL in multisite setups is also noted.

```php
Entry::query()->where('uri', 'blog/my-first-post')->first();

// Or with the shorthand method
Entry::findByUri('/blog/my-first-post');
```

--------------------------------

### Install Starter Kit with Config using Composer

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

This shell command installs a Statamic Starter Kit into a new project and includes the project's configuration. The `--with-config` option ensures that configuration files, like `starter-kit.yaml`, are preserved for future exports.

```shell
statamic new kung-fury-dev the-hoff/kung-fury-theme --with-config
```

--------------------------------

### Install Starter Kit Locally using Composer

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

This shell command installs a Statamic Starter Kit from a locally configured repository. The `--local` flag tells Composer to use the repository defined in the global `config.json`.

```shell
statamic new kung-fury-dev the-hoff/kung-fury-theme --local
```

--------------------------------

### Composer JSON for Local Repository Installation

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

This JSON object is added to a global Composer `config.json` file to register a local path as a repository. This allows for installing Starter Kits directly from a local directory using the `--local` option.

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

### Statamic Widget Vue Component Example

Source: https://v6.statamic.dev/widgets/building-a-widget

Example of a Statamic widget's Vue 3 component using `<script setup>`. It imports the `Widget` component from `@statamic/cms/ui` and defines props. The template uses the `<Widget>` component, passing a title and displaying a message prop.

```html
<script setup>
import { Widget } from '@statamic/cms/ui';

defineProps(['message']);
</script>

<template>
    <Widget title="LocalWeather">
        <div class="px-4 py-3">
            <p>👋 {{ message }}</p>
        </div>
    </Widget>
</template>
```

--------------------------------

### Get Entry by Collection, Slug, and Site using Statamic

Source: https://v6.statamic.dev/repositories/entry-repository

Shows how to query for an entry that matches a specific collection, slug, and site handle, useful in multisite installations.

```php
Entry::query()
    ->where('collection', 'team')
    ->where('slug', 'director')
    ->where('site', 'albuquerque')
    ->get();
```

--------------------------------

### Global Variables Localization: Multi-Site YAML Example

Source: https://v6.statamic.dev/getting-started/upgrade-guide/5-to-6

This snippet demonstrates the updated method for localizing global sets in multi-site installs. It shows how the 'sites' array in the global set's config file now dictates localization and origin mapping.

```yaml
# content/globals/seo.yaml

title: SEO

sites:

  en: null

  fr: en # Localized from en

  de: null # No origin

```

--------------------------------

### Create New Statamic Application (Shell)

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Navigates to the web root directory and creates a new Statamic application named 'example.com' using the Statamic CLI.

```shell
cd /var/www
statamic new example.com
```

--------------------------------

### Full Site Configuration Example

Source: https://v6.statamic.dev/control-panel/multi-site

An example of a complete site configuration in `resources/sites.yaml`, demonstrating options such as name, URL, locale, language, and custom attributes like theme.

```yaml
# resources/sites.yaml

en:
  name: English
  url: /
  locale: en_US
  lang: en
  attributes:
    theme: standard
```

--------------------------------

### Example Statamic Deployment Script using Shell

Source: https://v6.statamic.dev/deploying/ploi

A typical deployment script for a Statamic site on Ploi. It includes steps for pulling the latest code, installing dependencies, reloading the PHP service, clearing the cache, and building frontend assets.

```shell
cd /home/ploi/{example}.{tld}

git pull origin main

composer install --no-interaction --prefer-dist --optimize-autoloader

echo ... sudo-S service php8.1-fpm reload

php please cache:clear

npm ci && npm run production
```

--------------------------------

### Navigation Folder Structure Example

Source: https://v6.statamic.dev/tips/localizing-navigation

Illustrates the recommended folder structure for localized navigation in a multisite Statamic installation. Navigations are placed within their respective site directories.

```tree
content/navigation/
  nav.yaml
  site-one/
   nav.yaml
  site-two/
   nav.yaml
```

--------------------------------

### Verify Composer Installation (Shell)

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Checks if Composer has been installed correctly and is accessible from the command line.

```shell
composer
```

--------------------------------

### Display Recent Blog Posts in Antlers

Source: https://v6.statamic.dev/quick-start-guide

This Antlers template snippet fetches the 5 most recent entries from the 'blog' collection and displays them as a list of links. Each link includes the entry's title and publication date. It utilizes the `collection` tag with a `limit` parameter.

```antlers
<h1 class="text-2xl font-bold my-6">Welcome to my CyberSpace Place!</h1>

{{ content }}


<section class="border border-green-400 mt-12">

    <h2 class="p-5">Recent Blog Posts</h2>

    {{ collection:blog limit="5" }}

        <a href="{{ url }}" class="flex items-center justify-between p-5 border-t border-green-400 text-green-400 hover:text-green-900 hover:bg-green-400">

            <span>{{ title }}</span>

            <span class="text-green-900 text-sm">{{ date }}</span>

        </a>

    {{ /collection:blog }}

</section>
```

--------------------------------

### Install Nginx

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Installs the Nginx web server on a Debian-based system using apt. This is a prerequisite for serving web content.

```shell
sudo apt install nginx -y
```

--------------------------------

### PHP Post-Install Hook

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

This PHP code defines a non-namespaced class `StarterKitPostInstall` with a `handle` method. This method is executed after the Starter Kit is installed, allowing for custom logic such as outputting messages to the console.

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

### Selecting Between Multiple Modules in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Allows users to choose from multiple optional modules. The `options` key groups mutually exclusive module choices, providing a selection mechanism during installation.

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

### Create a New Statamic Project

Source: https://v6.statamic.dev/getting-started/installing/local

Creates a new Statamic project in the current directory. You will be prompted to choose between a blank site or a Starter Kit, and to set up your first super admin user.

```shell
statamic new project_name
```

--------------------------------

### Install Composer on Ubuntu

Source: https://v6.statamic.dev/installing/ubuntu

Downloads and installs the Composer dependency manager for PHP. It first downloads the installer script, then moves the composer.phar file to a globally accessible binary directory and makes it executable.

```shell
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
composer
```

--------------------------------

### Customizing Module Prompt Text in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Enables customization of the prompt text displayed to the user when an optional module is presented for installation. This allows for more user-friendly and branded interactions.

```yaml
modules:
  seo:
    prompt: 'Would you like some awesome SEO with that!?
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### Clone Statamic CMS Repository Locally

Source: https://v6.statamic.dev/contribution-guide

This command clones the statamic/cms repository from GitHub to your local machine. Ensure you have Git installed and have forked the repository to your GitHub account first. This step downloads the entire project history and files.

```shell
cd Code 
git clone https://github.com/your-username/cms.git 
Cloning into 'cms'...

remote: Enumerating objects: 86396, done.

remote: Counting objects: 100% (3025/3025), done.

remote: Compressing objects: 100% (1917/1917), done.

remote: Total 86396 (delta 1674), reused 2078 (delta 1085), pack-reused 83371

Receiving objects: 100% (86396/86396), 33.39 MiB | 5.76 MiB/s, done.

Resolving deltas: 100% (67201/67201), done.
```

--------------------------------

### Invalid Antlers Variable Name Example

Source: https://v6.statamic.dev/frontend/antlers

Shows an example of an invalid Antlers variable name that does not adhere to the specified naming conventions. Variable names must start with an alphabet character or underscore and follow specific rules.

```antlers
{{ this_iS-RiDicuL-ou5_ }}
```

--------------------------------

### GET /api/globals

Source: https://v6.statamic.dev/content-api

Retrieves all global sets. Supports site filtering for multi-site setups.

```APIDOC
## GET /api/globals

### Description
Gets all globals.

### Method
GET

### Endpoint
`/api/globals`

### Parameters
#### Query Parameters
- **site** (string) - Optional - Specifies the site to retrieve globals from in a multi-site configuration.

### Request Example
```
GET /api/globals?site=fr
```

### Response
#### Success Response (200)
- **data** (array) - An array of global sets, each with a `handle`, `api_url`, and other variables.

#### Response Example
```json
{
  "data": [
    {
      "handle": "global",
      "api_url": "http://example.com/api/globals/global",
      "foo": "bar"
    },
    {
      "handle": "another",
      "api_url": "http://example.com/api/globals/another",
      "baz": "qux"
    }
  ]
}
```
```

--------------------------------

### Setting Default Value for Optional Modules in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Configures an optional module to be installed by default. This is useful for essential modules or when the Starter Kit is installed non-interactively, ensuring certain components are always included.

```yaml
modules:
  seo:
    default: true
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### GET /api/taxonomies/{taxonomy}/terms

Source: https://v6.statamic.dev/content-api

Retrieves all terms within a specified taxonomy. Supports filtering by site for multi-site setups.

```APIDOC
## GET /api/taxonomies/{taxonomy}/terms

### Description
Gets terms in a taxonomy.

### Method
GET

### Endpoint
`/api/taxonomies/{taxonomy}/terms`

### Parameters
#### Query Parameters
- **filter[site]** (string) - Optional - Filters terms by a specific site in a multi-site configuration.

### Request Example
```
GET /api/taxonomies/categories/terms?filter[site]=fr
```

### Response
#### Success Response (200)
- **data** (array) - An array of taxonomy terms, each with a `title` property.

#### Response Example
```json
{
  "data": [
    {
      "title": "Music"
    }
  ],
  "links": {...},
  "meta": {...}
}
```
```

--------------------------------

### Basic Antlers View Example

Source: https://v6.statamic.dev/frontend/views

A simple example of an Antlers view file. Views are stored in the `resources/views` directory and contain the HTML for your site's frontend. This example demonstrates basic HTML structure within an Antlers file.

```antlers
<html>
  <body>
    <p>The invention of the shovel was ground breaking.</p>
  </body>
</html>
```

--------------------------------

### Install Statamic Collaboration Addon

Source: https://v6.statamic.dev/advanced-topics/cli

Installs the Statamic Collaboration addon and enables broadcasting in Laravel. This command sets up real-time collaboration features within Statamic.

```shell
php please install:collaboration
```

--------------------------------

### Composition API Vue Component Example

Source: https://v6.statamic.dev/upgrade-guide/vue-2-to-3

The same Vue component as above, but rewritten using the Composition API with `script setup`. This demonstrates the use of `ref`, `computed`, `watch`, and standard functions for reactive state management.

```vue
<script setup>
import { FooComponent, BarComponent } from './components';
import { Button, Card } from '@statamic/cms/ui';
import { ref, computed, watch } from 'vue';

const firstName = ref('John');
const lastName = ref('Smith');
const fullName = computed(() => `${firstName.value} ${lastName.value}`);

function changeName(first, last) {
    firstName.value = first;
    lastName.value = last;
}

watch(fullName, (newName) => console.log(`Name changed to ${newName}`));
</script>
```

--------------------------------

### GET /api/collections/{collection}/entries

Source: https://v6.statamic.dev/frontend/rest-api

Retrieves a list of entries within a specified collection. Supports filtering by site for multi-site setups.

```APIDOC
## GET /api/collections/{collection}/entries

Gets entries within a collection. If using Multi-Site, this endpoint serves from all sites at once. You can limit the fetched data to a specific site with a `site` filter.

### Endpoint
`/api/collections/{collection}/entries`

### Query Parameters
- **site** (string) - Optional - The site to filter entries by (e.g., `fr`).

### Response Example (Success 200)
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
```

--------------------------------

### Navigation YAML Configuration Example

Source: https://v6.statamic.dev/tips/localizing-navigation

Example of a base navigation YAML file. It defines the navigation's title, root status, and associated collections. This file serves as a template for site-specific configurations.

```yaml
# nav.yaml

title: Nav

root: true

collections:
  - pages
  - articles
```

--------------------------------

### Convert to Multisite Installation

Source: https://v6.statamic.dev/advanced-topics/cli

Converts a single-site Statamic installation to a multisite configuration. This command helps in setting up and managing multiple sites within a single Statamic project.

```shell
php please multisite
```

--------------------------------

### Check Nginx Service Status

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Verifies if the Nginx service is running and active after installation or configuration changes.

```shell
sudo systemctl status nginx
```

--------------------------------

### Including Composer Dependencies in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Specifies Composer dependencies to be bundled with the Starter Kit. These dependencies will be installed with the same version constraints as they were in the sandbox project during development.

```yaml
dependencies:
  - statamic/ssg
```

--------------------------------

### Install Statamic Eloquent Driver

Source: https://v6.statamic.dev/advanced-topics/cli

Installs and configures Statamic's Eloquent Driver package. This command is essential for using Eloquent models as your content driver.

```shell
php please install:eloquent-driver
```

--------------------------------

### Global Set Data Example (YAML)

Source: https://v6.statamic.dev/content-modeling/globals

An example YAML file containing the actual data for a global set named 'footer' within the 'default' site scope. This includes 'copyright' and 'flair' variables.

```yaml
# content/globals/default/footer.yaml
copyright: 2021 Neat Fake Company, LLC
flair: Made with ❤️ by humans
```

--------------------------------

### Get All Forms API

Source: https://v6.statamic.dev/content-api

Fetches a list of all forms available in the Statamic installation. Each form entry includes its handle, title, fields definition, and API URL.

```json
{
  "data": [
    {
      "handle": "contact",
      "title": "Contact",
      "fields": {
        "name": {...},
        "email": {...},
        "inquiry": {...}
      },
      "api_url": "http://example.com/api/forms/contact"
    },
    {
      "handle": "newsletter",
      "title": "Subscribe to Newsletter",
      "fields": {
        "email": {...}
      },
      "api_url": "http://example.com/api/forms/newsletter"
    }
  ]
}
```

--------------------------------

### Verify Local Package Symlink

Source: https://v6.statamic.dev/contribution-guide

After running the Composer commands, you can verify that your local fork of Statamic CMS is correctly linked by using the `composer show` command. This command will display the installation path of the 'statamic/cms' package, confirming it points to your local directory.

```shell
composer show statamic/cms --path
```

--------------------------------

### Install Composer Globally (Shell)

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Downloads and installs the latest version of Composer, a dependency manager for PHP. It then moves the composer executable to a globally accessible path and sets execute permissions.

```shell
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

--------------------------------

### Navigation YAML Configuration Example

Source: https://v6.statamic.dev/content-modeling/navigation

Shows an example of a navigation configuration file in YAML format. It includes the navigation's title, maximum depth, and a list of collections that can be used within the navigation.

```yaml
# content/navigation/footer.yaml

title: Footer

max_depth: 3

collections:

  - pages

  - posts

  - documents
```

--------------------------------

### Static HTML Example

Source: https://v6.statamic.dev/tags/route

This is a basic HTML example showing a hardcoded URL. While not dynamic, it serves as a reference point for what the generated URLs might look like.

```html
<form action="/bacon/6">

   ... yummy bacon goodness

</form>
```

--------------------------------

### Example Site Output

Source: https://v6.statamic.dev/variables/sites

Illustrates the expected HTML output after processing the site data through the templates, displaying information for each configured site.

```html
english
English Site
en_US
en
http://mysite.com/
ltr
bar


french
French Site
fr_FR
fr
http://mysite.com.fr/
ltr
baz
```

--------------------------------

### Composer Configuration for Path Repository

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Configures the `composer.json` file to include the starter kit's package directory as a path repository. This is crucial for making your starter kit updatable during development.

```json
{
    "name": "statamic/statamic",
    "require": [
        "the-hoff/kung-fury-theme": "dev-master"
    ],
    "repositories": [
        {
            "type": "path",
            "url": "package"
        }
    ]
}
```

--------------------------------

### Install Statamic Importer Addon

Source: https://v6.statamic.dev/knowledge-base/tips/importing-content

Installs the first-party Statamic Importer addon using Composer. This addon simplifies the process of importing entries, taxonomies, and users with a UI for mapping data.

```bash
composer require statamic/importer
```

--------------------------------

### Static HTML Edit Link

Source: https://v6.statamic.dev/variables/edit_url

An example of a static HTML link to edit a specific page. This is not dynamically generated by Statamic tags and would need manual updating.

```html
<a href="/cp/pages/edit/about-ye-old-me">Edit this page</a>
```

--------------------------------

### Composer JSON for Starter Kit Publishing

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

This JSON object represents the `composer.json` file for a Statamic Starter Kit. It includes the package name and extra configuration for Statamic, such as the display name and description.

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

### Set File Permissions for Statamic Installation (Shell)

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Grants necessary read, write, and execute permissions to the web server user ('www-data') for the Statamic application directory and its subdirectories to allow proper operation.

```shell
sudo chmod -R 755 /var/www/example.com
sudo chown -R www-data:www-data /var/www/example.com
sudo chown -R www-data:www-data /var/www/example.com/storage
sudo chown -R www-data:www-data /var/www/example.com/content
sudo chown -R www-data:www-data /var/www/example.com/bootstrap/cache
```

--------------------------------

### Statamic Sites API - Index Endpoint Example Output

Source: https://v6.statamic.dev/advanced-topics/sites-api

Provides an example of the JSON response structure when retrieving a list of sites using the Sites Index endpoint. This output includes site names, keys, domains, and creation timestamps.

```json
{
  "data": [
    {
      "name": "Wayne's World",
      "key": "pg4x2qrly2my8dl1",
      "domains": [
        "waynesworld.ca"
      ],
      "created_at": "2021-11-19 09:32:52"
    },
    {
      "name": "Bobby's World",
      "key": "1o0xe7rzdd9wq58j",
      "domains": [
        "bobbysworld.ca"
      ],
      "created_at": "2021-11-19 09:33:10"
    }
  ]
}
```

--------------------------------

### Get Terms by Taxonomy in a Collection

Source: https://v6.statamic.dev/backend-apis/resource-apis/term-repository

This example shows how to retrieve terms of a specific taxonomy that are associated with entries in a particular collection. It filters by both taxonomy and collection handles.

```php
Term::query()
    ->where('collection', 'blog')
    ->where('taxonomy', 'tags')
    ->get();
```

--------------------------------

### Write Basic PHPUnit Test for Statamic Addon

Source: https://v6.statamic.dev/addons/testing

Create a fundamental PHPUnit test for your Statamic addon by extending the addon's TestCase. This example demonstrates a simple assertion that true is true, serving as a starting point for more complex tests.

```php
<?php

namespace AcmeExampleTests;

use AcmeExampleTestsTestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
```

--------------------------------

### Deploy Statamic Code with Git

Source: https://v6.statamic.dev/getting-started/deploying/fortrabbit

These shell commands guide the user through initializing a Git repository, adding the fortrabbit remote, staging changes, committing them, and performing the initial push to the fortrabbit server. Subsequent pushes are simpler.

```shell
# 1. Initialize Git

git init



# 2. Add your Apps Git remote to your local repo

git remote add fortrabbit {{appname}}@deploy.{{region}}.frbit.com:{{appname}}.git



# 4. Add changes to Git

git add -A



# 5. Commit changes

git commit -m 'My first commit'



# 6. Initial push and upstream

git push -u fortrabbit main



# From there on only

git push
```

--------------------------------

### Install PHP and Required Modules (Shell)

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Installs PHP version 8.2+ and essential modules required for Statamic and its dependencies. The '-y' flag automatically confirms prompts.

```shell
sudo apt install php-common php-fpm php-json php-mbstring zip unzip php-zip php-cli php-xml php-tokenizer php-curl -y
```

--------------------------------

### Home Template Using Partial and Slot (Antlers)

Source: https://v6.statamic.dev/getting-started/quick-start-guide

The home template is updated to use the `blog/listing` partial. It passes a `limit="5"` and uses the partial as a tag pair (`{{ partial:blog/listing }}...{{ /partial:blog/listing }}`) to insert content (like 'Recent Blog Posts') into the partial's slot.

```antlers
<h1 class="text-2xl font-bold my-6">Welcome to my CyberSpace Place!</h1>

{{ content }}

{{ partial:blog/listing limit="5" }}
    <h2 class="p-5">Recent Blog Posts</h2>
{{ /partial:blog/listing }}
```

--------------------------------

### Get Current User Details (Blade)

Source: https://v6.statamic.dev/tags/user-profile

This Blade code snippet achieves the same as the Antlers example, fetching and displaying the current user's name. It uses the `<s:user>` tag and accesses the name via `{{ $name }}`. It also shows an example of aliasing the user data using `as="user"` for more complex scenarios.

```blade
<s:user>

  The current user's name is {{ $name }}.

</s:user>



{{-- Aliasing the user. --}}

<s:user

  as="user"

>

  The current user's name is {{ $user->name }}.

</s:user>
```

--------------------------------

### Last Modifier Example (Antlers)

Source: https://v6.statamic.dev/modifiers/last

Demonstrates using the 'last' modifier in Antlers to get the last 7 characters of a string or the last item from an array. No external dependencies are required.

```antlers
{{ title | last(7) }}

{{ array | last }}
```

--------------------------------

### Example HTML Output of File Path

Source: https://v6.statamic.dev/variables/path

Provides a sample of what the rendered HTML output might look like when displaying a file path. This is a static representation and may vary based on the actual asset. It's useful for understanding the final structure.

```html
img/black-bear-cubs.jpg
```

--------------------------------

### Initialize Statamic Starter Kit Configuration

Source: https://v6.statamic.dev/advanced-topics/cli

Creates a new starter kit configuration file. This command is the first step in defining the structure and content of a custom starter kit.

```shell
php please starter-kit:init
```

--------------------------------

### Get Singular Form of Word (YAML)

Source: https://v6.statamic.dev/modifiers/singular

This example shows how to utilize the 'singular' modifier within a YAML configuration or data structure in Statamic. It specifies the input word and applies the singular transformation.

```yaml
word: nickles
```

--------------------------------

### Update Package Lists and Installed Packages (Shell)

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Updates the local package index and upgrades existing packages. This ensures you have the latest versions and security patches before installing new software.

```shell
sudo apt-get update
sudo apt-get upgrade
```

--------------------------------

### Example Asset Sizes Output

Source: https://v6.statamic.dev/variables/size

This section provides example outputs for the file size of assets, illustrating the human-readable formats that can be displayed using the Statamic templating engines. These formats range from bytes to gigabytes.

```html
11 B

127.69 KB

1.5 MB

2 GB
```

--------------------------------

### Module-Specific Export Paths and Dependencies in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Allows optional modules to define their own export paths and dependencies, separate from the main Starter Kit configuration. This enables granular control over what gets included with specific modules.

```yaml
modules:
  seo:
    export_paths:
      - resources/css/seo.css
    dependencies:
      - statamic/seo-pro
```

--------------------------------

### Configure Starter Kit for Updates (YAML)

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

This configuration snippet demonstrates how to enable updates for a Statamic starter kit. By adding `updatable: true` to the `starter-kit.yaml` file, the starter kit will remain as a Composer dependency after installation, allowing for updates.

```yaml
+updatable: true 
export_paths: ...
```

--------------------------------

### Query Users in a Group - PHP

Source: https://v6.statamic.dev/backend-apis/resource-apis/user-group-repository

This example shows how to query all users belonging to a specific user group. It chains the `find` and `queryUsers` methods, followed by `get` to retrieve the user collection.

```php
UserGroup::find('editors')
    ->queryUsers()
    ->get();

```

--------------------------------

### Local Addon Installation in Composer (JSON)

Source: https://v6.statamic.dev/addons/building-an-addon

Configures a local addon to be installed via Composer using a path repository. Add the addon to 'require' and define its location in 'repositories'.

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

### Global Set Metadata Example (YAML)

Source: https://v6.statamic.dev/content-modeling/globals

An example YAML file defining the metadata for a global set named 'Footer'. This file specifies the title of the global set.

```yaml
# content/globals/footer.yaml
title: Footer
```

--------------------------------

### Full Example: Musicians Dictionary using MusicBrainz API in PHP

Source: https://v6.statamic.dev/fieldtypes/dictionaries

A complete Statamic Dictionary class named 'Artists' that fetches musician data from the MusicBrainz API. It utilizes caching for performance and defines 'options' and 'get' methods to retrieve and display artist information.

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

}
```

--------------------------------

### Install Statamic Static Site Generator Package

Source: https://v6.statamic.dev/advanced-topics/cli

Installs and configures Statamic's Static Site Generator (SSG) package. This command prepares your project for generating static HTML files from your Statamic content.

```shell
php please install:ssg
```

--------------------------------

### Compile CMS Assets with npm

Source: https://v6.statamic.dev/contribution-guide

These commands are used to compile frontend assets (stylesheets, JavaScript, Vue components) within the CMS repository. 'npm ci' installs dependencies, and 'npm run dev' (or 'build') compiles the assets into the designated output directory.

```shell
cd cms

npm ci

npm run dev  # or npm run build
```

--------------------------------

### Export Starter Kit Files

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Exports the relevant files from your Statamic sandbox into a specified directory, preparing them for distribution as a starter kit package. This directory should be version controlled.

```php
php please starter-kit:export ../kung-fury-theme
```

--------------------------------

### Create New Statamic Application without Interaction (Shell)

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

Creates a new Statamic application named 'example.com' using the Statamic CLI with the `--no-interaction` flag, simulating TTY mode for non-interactive environments.

```shell
script -q -c "statamic new --no-interaction example.com"
```

--------------------------------

### List Support Details

Source: https://v6.statamic.dev/advanced-topics/cli

Lists useful details about the Statamic installation and environment that can aid in support requests. This command gathers system information relevant for troubleshooting.

```shell
php please support:details
```

--------------------------------

### Customizing Select Module Prompts and Labels in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Provides options to customize the main prompt text, the text for skipping all options, and the labels for individual module choices within a selection group. Enhances user experience and branding.

```yaml
modules:
  js:
    prompt: 'Would you care for some JS?'
    skip_option: 'No, thank you!'
    options:
      vue:
        label: 'VueJS'
        export_paths:
          - resources/js/vue.js
      react:
        label: 'ReactJS'
        export_paths:
          - resources/js/react.js
      mootools:
        label: 'MooTools (will never die!)'
        export_paths:
          - resources/js/mootools.js
```

--------------------------------

### Get Item Data for Relationship Fieldtype

Source: https://v6.statamic.dev/extending/relationship-fieldtypes

This PHP method, `getItemData`, converts the stored item IDs (from the relationship field) back into meaningful data. This example fetches full tweet details from Twitter using their IDs.

```php
public function getItemData($values, $site = null)
{
    $tweets = Twitter::getStatusesLookup(['id' => implode(',', $values)]);

    return $this->formatTweets($tweets);
}
```

--------------------------------

### Get Collection Mount URL using Shorthand Syntax (Antlers and Blade)

Source: https://v6.statamic.dev/tags/mount_url

This snippet illustrates the shorthand syntax for the Mount URL tag in Statamic, allowing direct specification of the collection handle as an argument. It provides examples for both Antlers and Blade templating engines.

```antlers
<a href="{{ mount_url:blog }}">Read Our Blog</a>
```

```blade
<a href="{{ Statamic::tag('mount_url:blog') }}">Read Our Blog</a>
```

--------------------------------

### Static Slug Example in HTML

Source: https://v6.statamic.dev/variables/slug

Provides a basic HTML example showing a static title and its corresponding slug. This illustrates the direct relationship between a human-readable title and its URL-friendly slug representation.

```html
<h1>My Thoughts on Bacon</h1>

my-thoughts-on-bacon
```

--------------------------------

### Exporting Paths in starter-kit.yaml

Source: https://v6.statamic.dev/starter-kits/creating-a-starter-kit

Defines files and directories to be exported with a Statamic Starter Kit. This is crucial for distributing starter content, assets, and configuration files. Any file not listed will not be exported.

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

### Export Statamic Starter Kit

Source: https://v6.statamic.dev/advanced-topics/cli

Exports a starter kit package. This command is used to package a set of configurations, blueprints, and content as a reusable starter kit.

```shell
php please starter-kit:export
```

--------------------------------

### Get Entries by Date Range (PHP)

Source: https://v6.statamic.dev/backend-apis/resource-apis/entry-repository

Demonstrates how to query entries based on their date field. Examples include retrieving entries before a specific year and fetching all entries from today or within the last year using Carbon.

```php
// Get all Pre-Y2K news
Entry::query()
    ->where('collection', 'news')
    ->where('date', '<', '2000') 
    ->get();

// Get all of today's news (requires Illuminate\Support\Carbon)
use Illuminate\Support\Carbon;

Entry::query()
    ->where('collection', 'news')
    ->where('date', Carbon::parse('today'))
    ->get();

// Get the last 12 months of news (requires Illuminate\Support\Carbon)
use Illuminate\Support\Carbon;

Entry::query()
    ->where('collection', 'news')
    ->where('date', '>=', Carbon::parse('now')->subYears(1))
    ->get();
```

--------------------------------

### Nginx Configuration for Statamic

Source: https://v6.statamic.dev/installing/ubuntu

A sample Nginx server block configuration file for serving a Statamic site. It defines listening port, server name, document root, and specific location blocks for handling static files, PHP requests, and other directives.

```nginx
server {

    listen 80;

    server_name example.com;
    root /var/www/example.com/public;



    add_header X-Frame-Options "SAMEORIGIN";

    add_header X-XSS-Protection "1; mode=block";

    add_header X-Content-Type-Options "nosniff";



    index index.html index.htm index.php;



    charset utf-8;



    set $try_location @static;



    if ($request_method != GET) {

        set $try_location @not_static;

    }



    if ($args ~* "live-preview=(.*)") {

        set $try_location @not_static;

    }



    location / {

        try_files $uri $try_location;

    }



    location @static {

        try_files /static${uri}_$args.html $uri $uri/ /index.php?$args;

    }



    location @not_static {

        try_files $uri /index.php?$args;

    }



    location = /favicon.ico { access_log off; log_not_found off; }

    location = /robots.txt  { access_log off; log_not_found off; }



    error_page 404 /index.php;



    location ~ \.php$ {

        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;

        fastcgi_index index.php;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;

        include fastcgi_params;

    }



    location ~ /\.(?!well-known).* { 

        deny all;

    }

}

```

--------------------------------

### Basic Addon Test Structure (PHP)

Source: https://v6.statamic.dev/extending/testing-in-addons

Define a basic test for your addon by extending the custom TestCase class and implementing test methods. This example demonstrates a simple assertion that true is true, providing a starting point for more complex tests.

```php
<?php

namespace Acme\Example\Tests;

use Acme\Example\Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
```

--------------------------------

### Overview of Event Handling

Source: https://v6.statamic.dev/backend-apis/events

Explains how Statamic dispatches events and how to create listeners to handle them. Includes an example of registering an event listener in `EventServiceProvider.php`.

```APIDOC
## Event Handling Overview

Statamic dispatches various events throughout its codebase. To listen for an event, create an event listener, specify the event name, and then handle the event within the listener's `handle` method.

### Example Listener Setup

```php
use Statamic\Events\SomeEvent;

class SomeListener
{
    public function handle(SomeEvent $event)
    {
        // Handle the event
    }
}
```

### Registering Listeners

For applications with an `app/Providers/EventServiceProvider.php` file, register your event listeners in the `$listen` array:

```php
protected $listen = [
    'SomeEvent' => [
        'SomeListener',
    ],
];
```

For more in-depth details, refer to the Laravel documentation on events. Addons can also quickly register event listeners or subscribers.
```

--------------------------------

### Get Taxonomy Terms API

Source: https://v6.statamic.dev/content-api

Fetches all terms within a specified taxonomy. For multi-site setups, the `site` filter can be used to retrieve terms from a particular site. The response includes the terms data along with links and meta information.

```json
{
  "data": [
    {
      "title": "Music"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

```url
/api/taxonomies/{taxonomy}/terms?filter[site]=fr
```

--------------------------------

### GraphQL Query Example for Thumbnail Field

Source: https://v6.statamic.dev/frontend/graphql

An example GraphQL query demonstrating how to request the custom 'thumbnail' field with a 'width' argument from an entry.

```graphql
{
    entry(id: 1) {
        thumbnail(width: 100)
    }
}
```

--------------------------------

### Example Permalink URL

Source: https://v6.statamic.dev/variables/permalink

An example of the output generated by the permalink tag, demonstrating the full absolute URL to a piece of content on the website.

```html
http://example.com/posts/bacon
```

--------------------------------

### Configure Nginx Server Block for Statamic

Source: https://v6.statamic.dev/getting-started/installing/ubuntu

A sample Nginx server block configuration file for serving a Statamic site. It defines listening port, server name, root directory, security headers, index files, and PHP-FPM processing. It handles static and non-static requests, including live previews.

```nginx
server {

    listen 80;

    server_name example.com;
    root /var/www/example.com/public;



    add_header X-Frame-Options "SAMEORIGIN";

    add_header X-XSS-Protection "1; mode=block";

    add_header X-Content-Type-Options "nosniff";



    index index.html index.htm index.php;



    charset utf-8;



    set $try_location @static;



    if ($request_method != GET) {

        set $try_location @not_static;

    }



    if ($args ~* "live-preview=(.*)") {

        set $try_location @not_static;

    }



    location / {

        try_files $uri $try_location;

    }



    location @static {

        try_files /static${uri}_$args.html $uri $uri/ /index.php?$args;

    }



    location @not_static {

        try_files $uri /index.php?$args;

    }



    location = /favicon.ico { access_log off; log_not_found off; }

    location = /robots.txt  { access_log off; log_not_found off; }



    error_page 404 /index.php;



    location ~ \.php$ {

        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;

        fastcgi_index index.php;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;

        include fastcgi_params;

    }



    location ~ /\.(?!well-known).* {

        deny all;

    }

}

```

--------------------------------

### Implementing Augmentable Interface

Source: https://v6.statamic.dev/extending/augmentation

Shows a basic example of how a custom class, 'Product', can be marked as 'augmentable' by implementing the Statamic Augmentable interface.

```php
use Statamic\Contracts\Data\Augmentable;



class Product implements Augmentable

{

    //

}
```

--------------------------------

### Example Entry Index Data

Source: https://v6.statamic.dev/advanced-topics/stache

This is a plain text example of how entry data might be indexed within the Stache, showing a mapping of entry IDs to their corresponding titles.

```txt
entry-id-1: Entry One
entry-id-2: Entry Two
```