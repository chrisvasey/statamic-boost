<?php

declare(strict_types=1);

use ChrisVasey\StatamicBoost\Mcp\Tools\ListFieldtypes;
use ChrisVasey\StatamicBoost\Mcp\Tools\SearchStatamicDocs;
use Composer\InstalledVersions;
use Illuminate\Contracts\Console\Kernel;
use Laravel\Boost\Mcp\ToolRegistry;
use Laravel\Boost\Mcp\Tools\DatabaseQuery;
use Symfony\Component\Process\Process;

require getcwd().'/vendor/autoload.php';
$app = require getcwd().'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

assert(PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 5, 'The fresh install must run on PHP 8.5.');
foreach (['laravel/framework' => '13.', 'statamic/cms' => '6.', 'laravel/boost' => '2.'] as $package => $major) {
    assert(str_starts_with(InstalledVersions::getVersion($package), $major), "Unexpected installed version of {$package}.");
}
assert(! is_link(base_path('vendor/chrisvasey/statamic-boost')), 'The package must be installed as a copy.');

$boost = json_decode(file_get_contents(base_path('boost.json')), true, flags: JSON_THROW_ON_ERROR);
assert(($boost['environment'] ?? null) === 'statamic', 'Boost installation must preserve the Statamic environment.');
assert(in_array('inertia-laravel', $boost['exclude_guidelines'] ?? [], true), 'Statamic guideline exclusions must survive installation.');
assert(($boost['guidelines'] ?? false) === true && ($boost['mcp'] ?? false) === true, 'Boost guidelines and MCP must be enabled.');

$guidelines = file_get_contents(base_path('AGENTS.md'));
assert(str_contains($guidelines, '## Statamic CMS'), 'The generated guidelines must include the package guidelines.');
assert(str_contains($guidelines, '{{ collection:blog'), 'Statamic Antlers examples must render correctly.');
$mcp = json_decode(file_get_contents(base_path('.mcp.json')), true, flags: JSON_THROW_ON_ERROR);
assert(in_array('boost:mcp', $mcp['mcpServers']['laravel-boost']['args'] ?? [], true), 'The project MCP configuration must start Laravel Boost.');

$tools = ToolRegistry::getAvailableTools();
$statamicTools = array_filter($tools, fn (string $tool): bool => str_starts_with($tool, 'ChrisVasey\\StatamicBoost\\Mcp\\Tools\\'));
assert(count($statamicTools) === 12, 'All twelve Statamic MCP tools must be registered.');
assert(! in_array(DatabaseQuery::class, $tools, true), 'Statamic mode must exclude database tools.');

foreach ([ListFieldtypes::class => [], SearchStatamicDocs::class => ['queries' => ['collections'], 'limit' => 1]] as $tool => $arguments) {
    $process = new Process([PHP_BINARY, 'artisan', 'boost:execute-tool', $tool, base64_encode(json_encode($arguments, JSON_THROW_ON_ERROR))], base_path());
    $process->mustRun();
    $response = json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
    assert($response['isError'] === false, "{$tool} must execute successfully.");
    $data = json_decode($response['content'][0]['text'], true, flags: JSON_THROW_ON_ERROR);
    if ($tool === ListFieldtypes::class) {
        assert(in_array('text', array_column($data['fieldtypes'], 'handle'), true), 'Core fieldtypes must be available.');
    } else {
        assert(count($data['results']['collections']) > 0, 'The installed documentation bundle must be searchable.');
    }
}

echo "Fresh installation verified: saved environment, guidelines, MCP configuration and runtime tools.\n";
