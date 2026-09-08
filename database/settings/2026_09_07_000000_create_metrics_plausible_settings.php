<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('metrics-plausible', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('api_key', env('PLAUSIBLE_API_KEY', ''));
            $blueprint->add('site_id', env('PLAUSIBLE_SITE_ID', ''));
            $blueprint->add('base_url', env('PLAUSIBLE_BASE_URL', 'https://plausible.io'));
        });
    }
};
