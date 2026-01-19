<?php

declare(strict_types=1);

namespace ChrisVasey\StatamicBoost\Console;

use ChrisVasey\StatamicBoost\EnvironmentDetector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;

class InstallCommand extends Command
{
    protected $signature = 'statamic-boost:install
        {--env= : Environment type (statamic, hybrid, or laravel)}';

    protected $description = 'Configure Statamic Boost environment and run boost:install';

    public function handle(EnvironmentDetector $detector): int
    {
        // Handle --no-interaction mode (CI/CD)
        if ($this->option('no-interaction') || $this->option('env')) {
            return $this->handleNonInteractive($detector);
        }

        $this->displayHeader();

        // Show current environment detection
        $this->displayDetectedEnvironment($detector);

        // Prompt for environment type
        $environment = $this->selectEnvironment($detector);

        // Save to both boost.json and config file
        $this->saveEnvironmentConfig($environment);

        // Run boost:install
        $this->info("\nRunning boost:install...\n");
        $this->call('boost:install');

        return self::SUCCESS;
    }

    protected function handleNonInteractive(EnvironmentDetector $detector): int
    {
        $environment = $this->option('env');

        // Auto-detect if not specified
        if (! $environment) {
            if (! $detector->isStatamicInstalled()) {
                $environment = 'laravel';
            } elseif ($detector->isStatamicCentric()) {
                $environment = 'statamic';
            } else {
                $environment = 'hybrid';
            }
            $this->info("Auto-detected environment: {$environment}");
        }

        // Validate environment option
        $valid = ['statamic', 'hybrid', 'laravel'];
        if (! in_array($environment, $valid, true)) {
            $this->error("Invalid environment: {$environment}. Must be one of: ".implode(', ', $valid));

            return self::FAILURE;
        }

        $this->saveEnvironmentConfig($environment);

        $this->call('boost:install', ['--no-interaction' => true]);

        return self::SUCCESS;
    }

    protected function displayHeader(): void
    {
        note($this->logo());
        intro('✦ Statamic Boost :: Install ✦');
    }

    protected function logo(): string
    {
        return <<<'HEADER'
        ███████╗████████╗ █████╗ ████████╗ █████╗ ███╗   ███╗██╗ ██████╗
        ██╔════╝╚══██╔══╝██╔══██╗╚══██╔══╝██╔══██╗████╗ ████║██║██╔════╝
        ███████╗   ██║   ███████║   ██║   ███████║██╔████╔██║██║██║
        ╚════██║   ██║   ██╔══██║   ██║   ██╔══██║██║╚██╔╝██║██║██║
        ███████║   ██║   ██║  ██║   ██║   ██║  ██║██║ ╚═╝ ██║██║╚██████╗
        ╚══════╝   ╚═╝   ╚═╝  ╚═╝   ╚═╝   ╚═╝  ╚═╝╚═╝     ╚═╝╚═╝ ╚═════╝
        HEADER;
    }

    protected function displayDetectedEnvironment(EnvironmentDetector $detector): void
    {
        $summary = $detector->getEnvironmentSummary();

        $this->newLine();
        $this->info('Detected environment:');

        if ($summary['statamic_installed']) {
            $this->line('  - Statamic is installed');

            if ($summary['has_runway']) {
                $this->line('  - Runway addon detected (Eloquent integration)');
            }

            if ($summary['has_custom_models']) {
                $this->line('  - Custom Eloquent models found');
            }

            if ($summary['uses_eloquent_users']) {
                $this->line('  - Using Eloquent for users');
            }

            if ($summary['statamic_centric']) {
                $this->line('  - Detected as: Statamic-centric (flat-file)');
            } else {
                $this->line('  - Detected as: Hybrid Laravel+Statamic');
            }
        } else {
            $this->line('  - Statamic is NOT installed');
            $this->line('  - Detected as: Laravel only');
        }

        $this->newLine();
    }

    protected function selectEnvironment(EnvironmentDetector $detector): string
    {
        // Determine default based on detection
        $default = 'hybrid';
        if (! $detector->isStatamicInstalled()) {
            $default = 'laravel';
        } elseif ($detector->isStatamicCentric()) {
            $default = 'statamic';
        }

        return select(
            label: 'What type of project is this?',
            options: [
                'statamic' => 'Statamic Only - Flat-file CMS, minimal Laravel (excludes DB/Eloquent guidelines)',
                'hybrid' => 'Laravel + Statamic - Full hybrid app (includes all guidelines)',
                'laravel' => 'Laravel Only - Skip Statamic guidelines entirely',
            ],
            default: $default,
            hint: 'This affects which AI guidelines and MCP tools are included'
        );
    }

    protected function saveEnvironmentConfig(string $environment): void
    {
        // Save to boost.json
        $this->saveToBoostJson($environment);

        // Update the published config file if it exists
        $this->updatePublishedConfig($environment);

        $this->info("Environment set to: {$environment}");
    }

    protected function saveToBoostJson(string $environment): void
    {
        $boostJsonPath = base_path('boost.json');
        $config = [];

        try {
            if (File::exists($boostJsonPath)) {
                $existing = File::get($boostJsonPath);
                $config = json_decode($existing, true) ?? [];
            }

            $config['environment'] = $environment;

            // Exclude Inertia guidelines for Statamic-only environments
            // Statamic's CP uses Inertia internally, causing false detection
            if ($environment === 'statamic') {
                $config['exclude_guidelines'] = [
                    'inertia-laravel',
                    'inertia-react',
                    'inertia-vue',
                    'inertia-svelte',
                ];
            } elseif (isset($config['exclude_guidelines'])) {
                // Remove Inertia exclusions if switching away from statamic-only
                $config['exclude_guidelines'] = array_values(array_filter(
                    $config['exclude_guidelines'],
                    fn ($g) => ! str_starts_with($g, 'inertia-')
                ));

                if (empty($config['exclude_guidelines'])) {
                    unset($config['exclude_guidelines']);
                }
            }

            ksort($config);

            File::put(
                $boostJsonPath,
                json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
            );
        } catch (\JsonException $e) {
            $this->error("Failed to parse boost.json: {$e->getMessage()}");
        }
    }

    protected function updatePublishedConfig(string $environment): void
    {
        $configPath = config_path('statamic-boost.php');

        if (! File::exists($configPath)) {
            // Config not published yet, it will use the environment from boost.json
            return;
        }

        $content = File::get($configPath);

        // Update the environment value in the config file
        $patterns = [
            // Match: 'environment' => env('STATAMIC_BOOST_ENV', 'hybrid'),
            "/('environment'\s*=>\s*env\s*\(\s*'STATAMIC_BOOST_ENV'\s*,\s*')([^']+)('\s*\))/",
            // Match: 'environment' => 'hybrid',
            "/('environment'\s*=>\s*')([^']+)(')/",
        ];

        $replaced = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "\${1}{$environment}\${3}", $content);
                $replaced = true;
                break;
            }
        }

        if ($replaced) {
            File::put($configPath, $content);
        }
    }
}
