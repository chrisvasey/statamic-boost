<?php

namespace ChrisVasey\StatamicBoost\Tests;

use ChrisVasey\StatamicBoost\StatamicBoostServiceProvider;
use Laravel\Boost\BoostServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Statamic\Providers\StatamicServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            StatamicServiceProvider::class,
            BoostServiceProvider::class,
            StatamicBoostServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('statamic.users.repository', 'file');
    }
}
