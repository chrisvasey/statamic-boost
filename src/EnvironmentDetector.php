<?php

namespace ChrisVasey\StatamicBoost;

use Illuminate\Support\Facades\File;

class EnvironmentDetector
{
    protected ?bool $cachedIsStatamicCentric = null;

    public function isStatamicInstalled(): bool
    {
        return class_exists(\Statamic\Statamic::class);
    }

    public function getStatamicMajorVersion(): ?int
    {
        if (! $this->isStatamicInstalled()) {
            return null;
        }

        $version = \Composer\InstalledVersions::getVersion('statamic/cms');

        return $version ? (int) explode('.', $version)[0] : null;
    }

    public function isStatamicCentric(): bool
    {
        if ($this->cachedIsStatamicCentric !== null) {
            return $this->cachedIsStatamicCentric;
        }

        return $this->cachedIsStatamicCentric = $this->detectStatamicCentric();
    }

    public function isHybridApp(): bool
    {
        return $this->isStatamicInstalled() && ! $this->isStatamicCentric();
    }

    public function hasRunwayAddon(): bool
    {
        return class_exists(\StatamicRadPack\Runway\Runway::class);
    }

    public function hasCustomEloquentModels(): bool
    {
        $modelsPath = app_path('Models');

        if (! File::isDirectory($modelsPath)) {
            return false;
        }

        $modelFiles = File::files($modelsPath);

        foreach ($modelFiles as $file) {
            $filename = $file->getFilename();

            // Skip the default User model
            if ($filename === 'User.php') {
                continue;
            }

            // Any other model file indicates custom models
            if (str_ends_with($filename, '.php')) {
                return true;
            }
        }

        return false;
    }

    public function usesEloquentUsers(): bool
    {
        // Check if Statamic is configured to use eloquent for users
        return config('statamic.users.repository') === 'eloquent';
    }

    protected function detectStatamicCentric(): bool
    {
        if (! $this->isStatamicInstalled()) {
            return false;
        }

        // If Runway is installed, it's likely a hybrid app
        if ($this->hasRunwayAddon()) {
            return false;
        }

        // If there are custom Eloquent models (beyond User.php), it's hybrid
        if ($this->hasCustomEloquentModels()) {
            return false;
        }

        // If users are stored in Eloquent (not flat file), consider it hybrid
        if ($this->usesEloquentUsers()) {
            return false;
        }

        // Otherwise, it's a Statamic-centric flat-file site
        return true;
    }

    public function clearCache(): void
    {
        $this->cachedIsStatamicCentric = null;
    }

    public function getEnvironmentSummary(): array
    {
        return [
            'statamic_installed' => $this->isStatamicInstalled(),
            'statamic_centric' => $this->isStatamicCentric(),
            'hybrid_app' => $this->isHybridApp(),
            'has_runway' => $this->hasRunwayAddon(),
            'has_custom_models' => $this->hasCustomEloquentModels(),
            'uses_eloquent_users' => $this->usesEloquentUsers(),
        ];
    }
}
