<?php

namespace Jeffersongoncalves\MetricsPlausible;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MetricsPlausibleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-metrics-plausible')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
