<?php

namespace Jeffersongoncalves\MetricsPlausible\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffersongoncalves\MetricsPlausible\MetricsPlausible
 */
class MetricsPlausible extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-metrics-plausible';
    }
}
