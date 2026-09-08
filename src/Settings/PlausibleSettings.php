<?php

namespace JeffersonGoncalves\MetricsPlausible\Settings;

use Spatie\LaravelSettings\Settings;

class PlausibleSettings extends Settings
{
    public string $api_key;

    public string $site_id;

    public string $base_url;

    public static function group(): string
    {
        return 'metrics-plausible';
    }
}
