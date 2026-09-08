<?php

namespace Jeffersongoncalves\MetricsPlausible\Tests;

use Jeffersongoncalves\MetricsPlausible\MetricsPlausibleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MetricsPlausibleServiceProvider::class,
        ];
    }
}
