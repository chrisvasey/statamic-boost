<?php

use ChrisVasey\StatamicBoost\Mcp\Tools\SearchStatamicDocs;
use ChrisVasey\StatamicBoost\Scripts\DocsBundle;
use Illuminate\Filesystem\Filesystem;

require_once dirname(__DIR__, 2).'/scripts/DocsBundle.php';

beforeEach(function () {
    $this->bundleDirectory = sys_get_temp_dir().'/statamic-docs-bundle-'.bin2hex(random_bytes(8));
    $this->bundleSource = $this->bundleDirectory.'/source';
    $this->bundleOutput = $this->bundleDirectory.'/bundle.md';
    $this->bundleCommit = str_repeat('a', 40);
    createDocsBundleFixture($this->bundleSource);
});

afterEach(function () {
    (new Filesystem)->deleteDirectory($this->bundleDirectory);
});

function createDocsBundleFixture(string $directory, string $major = '6'): void
{
    $collections = ['tags', 'modifiers', 'fieldtypes', 'variables', 'widgets', 'tips', 'troubleshooting'];
    $collections = array_merge($collections, $major === '5' ? ['docs', 'extending-docs', 'reference', 'repositories'] : ['pages', 'resource_apis']);

    foreach ($collections as $collection) {
        mkdir($directory.'/content/collections/'.$collection, 0755, true);
        file_put_contents($directory.'/content/collections/'.$collection.'/example.md', "---\ntitle: {$collection} example\n---\nDocumentation for {$collection}.\n");
    }
}

it('builds deterministic bundles that the documentation search can parse', function () {
    $file = $this->bundleSource.'/content/collections/pages/example.md';
    file_put_contents($file, "---\ntitle: Boundary example\nintro: Searchable introduction.\n---\nBefore the divider.\n-------------------------\n### A subsection\nAfter the divider.\n```antlers\n{{ collection:blog }}{{ title }}{{ /collection:blog }}\n```\n");

    $exporter = new DocsBundle;
    $first = $exporter->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput);
    $content = file_get_contents($this->bundleOutput);
    touch($file, 1234567890);
    $second = $exporter->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput);
    $sections = (new ReflectionMethod(SearchStatamicDocs::class, 'parseDocs'))->invoke(new SearchStatamicDocs, $this->bundleOutput);

    expect(file_get_contents($this->bundleOutput))->toBe($content)
        ->and($second)->toBe($first)
        ->and($first['pages'])->toBe(9)
        ->and($first['sha256'])->toBe(hash('sha256', $content))
        ->and($sections)->toHaveCount(9)
        ->and($sections[0]['title'])->toBe('Boundary example')
        ->and($sections[0]['source'])->toBe('https://github.com/statamic/docs/blob/'.$this->bundleCommit.'/content/collections/pages/example.md')
        ->and($sections[0]['content'])->toContain('After the divider.', '{{ collection:blog }}{{ title }}{{ /collection:blog }}');
});

it('preserves frontmatter-only fieldtype documentation and options', function () {
    file_put_contents($this->bundleSource.'/content/collections/fieldtypes/example.md', <<<'MARKDOWN'
---
title: Text
description: A simple text input field.
overview: >
  Store short unformatted text.
options:
  - name: character_limit
    type: integer
    description: Enforce a hard limit with the max validation rule.
id: irrelevant-entry-id
screenshot: fieldtypes/screenshots/text.webp
updated_at: 12345678
---
MARKDOWN);

    (new DocsBundle)->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput);

    expect(file_get_contents($this->bundleOutput))
        ->toContain('### Text', 'A simple text input field.', 'Store short unformatted text.', 'name: character_limit', 'type: integer', 'max validation rule')
        ->not->toContain('irrelevant-entry-id', 'screenshots/text.webp', 'updated_at');
});

it('creates portable source URLs for nested files from a native filesystem path', function () {
    $directory = $this->bundleSource.'/content/collections/pages/nested directory';
    mkdir($directory);
    file_put_contents($directory.'/nested example.md', "---\ntitle: Nested example\n---\nNested documentation.\n");

    (new DocsBundle)->write(realpath($this->bundleSource), '6', $this->bundleCommit, $this->bundleOutput);

    $sections = (new ReflectionMethod(SearchStatamicDocs::class, 'parseDocs'))->invoke(new SearchStatamicDocs, $this->bundleOutput);
    $nested = array_values(array_filter($sections, fn (array $section): bool => $section['title'] === 'Nested example'));

    expect($nested)->toHaveCount(1)
        ->and($nested[0]['source'])->toBe('https://github.com/statamic/docs/blob/'.$this->bundleCommit.'/content/collections/pages/nested%20directory/nested%20example.md');
});

it('retains tag parameters variables and extra Glide reference fields', function () {
    file_put_contents($this->bundleSource.'/content/collections/tags/example.md', <<<'MARKDOWN'
---
title: Collection
intro: Fetch entries from a collection.
parameters:
  - name: from|folder|use
    type: string|array
    required: false
    description: Choose the collection.
variables:
  - name: first
    type: boolean
    description: The first entry in the loop.
variables_content: Loop variables are available in Antlers.
shape:
  - name: width
    type: integer
filters:
  - name: blur
    type: integer
other:
  - name: preset
    type: string
---
## Examples
{{ collection:blog limit="5" }}
MARKDOWN);

    (new DocsBundle)->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput);

    expect(file_get_contents($this->bundleOutput))
        ->toContain('name: from|folder|use', 'type: string|array', 'required: false', 'name: first', 'Loop variables are available in Antlers.', 'name: width', 'name: blur', 'name: preset', '{{ collection:blog limit="5" }}');
});

it('omits drafts redirects empty listings and collections outside the allowlist', function () {
    $directory = $this->bundleSource.'/content/collections/pages/';
    file_put_contents($directory.'draft.md', "---\ntitle: Hidden draft\npublished: false\n---\nDo not publish.\n");
    file_put_contents($directory.'draft-status.md', "---\ntitle: Draft status\ndraft: true\n---\nDo not publish.\n");
    file_put_contents($directory.'redirect.md', "---\ntitle: Redirect page\nblueprint: link\nredirect:\n  url: '@child'\n  status: 301\n---\n");
    file_put_contents($directory.'link.md', "---\ntitle: External link\nlink: https://statamic.com\n---\n");
    file_put_contents($directory.'empty.md', "---\ntitle: Generated listing\ntemplate: fieldtypes/index\n---\n");
    mkdir($this->bundleSource.'/content/collections/adverts');
    file_put_contents($this->bundleSource.'/content/collections/adverts/advert.md', 'Not even valid frontmatter.');

    $result = (new DocsBundle)->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput);

    expect($result['pages'])->toBe(9)
        ->and(file_get_contents($this->bundleOutput))->not->toContain('Hidden draft', 'Draft status', 'Redirect page', 'External link', 'Generated listing', 'Not even valid');
});

it('supports the older v5 collection layout and missing variable titles', function () {
    $source = $this->bundleDirectory.'/v5';
    createDocsBundleFixture($source, '5');
    file_put_contents($source.'/content/collections/variables/is_video.md', "---\ntypes: [asset]\nid: variable-id\n---\nWhether the asset is a video.\n");

    $result = (new DocsBundle)->write($source, '5', $this->bundleCommit, $this->bundleOutput);

    expect($result['pages'])->toBe(12)
        ->and(file_get_contents($this->bundleOutput))->toContain('### is_video', '/content/collections/extending-docs/example.md', '/content/collections/repositories/example.md');
});

it('preserves an existing bundle when source frontmatter cannot be parsed', function (string $source) {
    file_put_contents($this->bundleOutput, 'Previously verified documentation.');
    file_put_contents($this->bundleSource.'/content/collections/pages/example.md', $source);

    expect(fn () => (new DocsBundle)->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput))->toThrow(RuntimeException::class)
        ->and(file_get_contents($this->bundleOutput))->toBe('Previously verified documentation.');
})->with([
    'invalid YAML' => "---\ntitle: [unfinished\n---\nBody\n",
    'missing closing boundary' => "---\ntitle: No boundary\nBody\n",
    'non-mapping YAML' => "---\n- title\n- example\n---\nBody\n",
    'invalid published status' => "---\ntitle: Example\npublished: maybe\n---\nBody\n",
    'unsafe YAML object tag' => "---\ntitle: !php/object 'untrusted'\n---\nBody\n",
]);

it('rejects unsupported versions invalid commits and incomplete snapshots', function () {
    $exporter = new DocsBundle;

    expect(fn () => $exporter->write($this->bundleSource, '7', $this->bundleCommit, $this->bundleOutput))->toThrow(RuntimeException::class, 'major must be 5 or 6')
        ->and(fn () => $exporter->write($this->bundleSource, '6', 'main', $this->bundleOutput))->toThrow(RuntimeException::class, '40-character Git SHA');

    unlink($this->bundleSource.'/content/collections/tags/example.md');

    expect(fn () => $exporter->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput))->toThrow(RuntimeException::class, 'no published documentation in tags')
        ->and(file_exists($this->bundleOutput))->toBeFalse();

    rmdir($this->bundleSource.'/content/collections/tags');

    expect(fn () => $exporter->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput))->toThrow(RuntimeException::class, 'missing or linked documentation directory');
});

it('rejects linked source files without replacing a verified bundle', function () {
    file_put_contents($this->bundleOutput, 'Verified bundle.');
    symlink($this->bundleOutput, $this->bundleSource.'/content/collections/pages/linked.md');

    expect(fn () => (new DocsBundle)->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput))->toThrow(RuntimeException::class, 'Linked documentation input')
        ->and(file_get_contents($this->bundleOutput))->toBe('Verified bundle.');
});

it('rejects linked documentation directories without replacing a verified bundle', function () {
    $directory = $this->bundleSource.'/content/collections/pages';
    $outside = $this->bundleDirectory.'/outside-pages';
    rename($directory, $outside);
    symlink($outside, $directory);
    file_put_contents($this->bundleOutput, 'Verified bundle.');

    expect(fn () => (new DocsBundle)->write($this->bundleSource, '6', $this->bundleCommit, $this->bundleOutput))->toThrow(RuntimeException::class, 'missing or linked documentation directory')
        ->and(file_get_contents($this->bundleOutput))->toBe('Verified bundle.');
});
