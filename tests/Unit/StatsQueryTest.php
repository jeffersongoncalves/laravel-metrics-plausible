<?php

use JeffersonGoncalves\MetricsPlausible\Enums\DateRange;
use JeffersonGoncalves\MetricsPlausible\Enums\Dimension;
use JeffersonGoncalves\MetricsPlausible\Enums\Metric;
use JeffersonGoncalves\MetricsPlausible\Queries\StatsQuery;

it('falls back to the configured site id and visitors metric', function (): void {
    expect(StatsQuery::make()->toArray())->toBe([
        'site_id' => 'example.com',
        'metrics' => ['visitors'],
        'date_range' => '30d',
    ]);
});

it('builds a full query payload', function (): void {
    $payload = StatsQuery::make()
        ->site('other.com')
        ->metrics(Metric::Visitors, Metric::Pageviews)
        ->dateRange(DateRange::Last7Days)
        ->dimensions(Dimension::Page)
        ->filter('is', Dimension::Country, ['BR'])
        ->orderBy(Metric::Visitors, 'desc')
        ->limit(10)
        ->offset(20)
        ->withImports()
        ->toArray();

    expect($payload)->toBe([
        'site_id' => 'other.com',
        'metrics' => ['visitors', 'pageviews'],
        'date_range' => '7d',
        'dimensions' => ['event:page'],
        'filters' => [['is', 'visit:country', ['BR']]],
        'order_by' => [['visitors', 'desc']],
        'pagination' => ['limit' => 10, 'offset' => 20],
        'include' => ['imports' => true],
    ]);
});

it('turns a date pair into an explicit range', function (): void {
    $payload = StatsQuery::make()->between('2026-01-01', '2026-01-31')->toArray();

    expect($payload['date_range'])->toBe(['2026-01-01', '2026-01-31']);
});

it('wraps a scalar filter value in a list', function (): void {
    $payload = StatsQuery::make()->filter('is', Dimension::Device, 'Desktop')->toArray();

    expect($payload['filters'])->toBe([['is', 'visit:device', ['Desktop']]]);
});

it('builds custom property dimensions', function (): void {
    expect(Dimension::property('plan'))->toBe('event:props:plan');
});
