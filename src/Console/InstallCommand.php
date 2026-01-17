<?php

declare(strict_types=1);

namespace ChrisVasey\StatamicBoost\Console;

use ChrisVasey\StatamicBoost\EnvironmentDetector;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Boost\Support\Config as BoostConfig;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;

class InstallCommand extends Command
{
    protected $signature = 'statamic-boost:install';

    protected $description = 'Configure Statamic Boost environment and run boost:install';

    public function handle(EnvironmentDetector $detector, BoostConfig $boostConfig): int
    {
        $this->displayHeader();

        // Show current environment detection
        $this->displayDetectedEnvironment($detector);

        // Prompt for environment type
        $environment = $this->selectEnvironment($detector);

        // Save to both boost.json and config file
        $this->saveEnvironmentConfig($environment, $boostConfig);

        // Run boost:install
        $this->info("\nRunning boost:install...\n");
        $this->call('boost:install');

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

    protected function saveEnvironmentConfig(string $environment, BoostConfig $boostConfig): void
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

        if (File::exists($boostJsonPath)) {
            $config = json_decode(File::get($boostJsonPath), true) ?? [];
        }

        $config['environment'] = $environment;
        ksort($config);

        File::put(
            $boostJsonPath,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );
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
            "/('environment'\s*=>\s*')([^']+)('/",
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
