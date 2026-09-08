<?php

namespace JeffersonGoncalves\MetricsPlausible;

use Illuminate\Support\Facades\Config;
use JeffersonGoncalves\MetricsPlausible\Settings\PlausibleSettings;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MetricsPlausibleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('metrics-plausible');
    }

    public function packageRegistered(): void
    {
        Config::set('settings.settings', array_merge(
            Config::get('settings.settings', []),
            [PlausibleSettings::class]
        ));

        $this->app->singleton(PlausibleClient::class, function () {
            $settings = app(PlausibleSettings::class);

            return new PlausibleClient(
                apiKey: $settings->api_key,
                baseUrl: $settings->base_url,
            );
        });

        $this->app->singleton('metrics-plausible', function ($app) {
            return new Plausible($app->make(PlausibleClient::class));
        });

        $this->app->alias('metrics-plausible', Plausible::class);
    }

    public function packageBooted(): void
    {
        $migrationsPath = __DIR__.'/../database/settings';

        Config::set('settings.migrations_paths', array_merge(
            [$migrationsPath],
            Config::get('settings.migrations_paths', [])
        ));

        $this->publishes([
            $migrationsPath => database_path('settings'),
        ], 'metrics-plausible-settings-migrations');
    }
}
