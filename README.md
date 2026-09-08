<div class="filament-hidden">

![Laravel Metrics Plausible](https://raw.githubusercontent.com/jeffersongoncalves/laravel-metrics-plausible/main/art/jeffersongoncalves-laravel-metrics-plausible.png)

</div>

# Laravel Metrics Plausible

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-metrics-plausible.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-metrics-plausible)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-metrics-plausible/pint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-metrics-plausible/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-metrics-plausible/phpstan.yml?branch=main&label=PHPStan&style=flat-square)](https://github.com/jeffersongoncalves/laravel-metrics-plausible/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-metrics-plausible.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-metrics-plausible)

Laravel package to interact with the [Plausible Analytics](https://plausible.io) Stats API v2. Fetch visitors, pageviews, sources, countries, devices, goal conversions and realtime visitors, and manage sites and goals — for dashboards, reports and automations.

Works with both Plausible Cloud and self-hosted Plausible Community Edition.

Settings are stored in the database via [spatie/laravel-settings](https://github.com/spatie/laravel-settings) — no config files needed.

> Looking to add the tracking script to your Blade layout instead? Use [jeffersongoncalves/laravel-plausible](https://github.com/jeffersongoncalves/laravel-plausible).

## Installation

```bash
composer require jeffersongoncalves/laravel-metrics-plausible
```

Run migrations to create the settings:

```bash
php artisan migrate
```

## Configuration

After migration, the settings are seeded from environment variables:

```env
PLAUSIBLE_API_KEY=your-api-key
PLAUSIBLE_SITE_ID=example.com
PLAUSIBLE_BASE_URL=https://plausible.io
```

Create the API key under **Settings → API Keys** in your Plausible account. `PLAUSIBLE_SITE_ID` is the site domain as registered in Plausible. Self-hosting? Point `PLAUSIBLE_BASE_URL` at your own instance.

You can also update settings programmatically:

```php
use JeffersonGoncalves\MetricsPlausible\Settings\PlausibleSettings;

$settings = app(PlausibleSettings::class);
$settings->api_key = 'new-key';
$settings->site_id = 'example.com';
$settings->base_url = 'https://analytics.example.com';
$settings->save();
```

## Usage

```php
use JeffersonGoncalves\MetricsPlausible\Enums\DateRange;
use JeffersonGoncalves\MetricsPlausible\Enums\Dimension;
use JeffersonGoncalves\MetricsPlausible\Enums\Metric;
use JeffersonGoncalves\MetricsPlausible\Facades\Plausible;
```

Every method takes an optional `$siteId` as its last argument, defaulting to the configured site.

### Aggregate totals

```php
$stats = Plausible::aggregate();

$stats->visitors();       // 1234
$stats->visits();         // 1500
$stats->pageviews();      // 4321
$stats->bounceRate();     // 41.5
$stats->visitDuration();  // 96.0

// Custom metrics and period
$stats = Plausible::aggregate([Metric::Visitors, Metric::Events], DateRange::Last7Days);
$stats->get('events');
```

### Timeseries

```php
// Daily visitors and pageviews over the last 30 days
$rows = Plausible::timeseries();

foreach ($rows as $row) {
    echo $row->label.': '.$row->visitors();  // 2026-09-01: 120
}

// Hourly, today
$rows = Plausible::timeseries(DateRange::Day, Dimension::TimeHour);

// Monthly, last 12 months
$rows = Plausible::timeseries(DateRange::Last12Months, Dimension::TimeMonth);
```

### Breakdowns

```php
$pages = Plausible::pages(DateRange::Last30Days, limit: 10);
$entry = Plausible::entryPages();
$exit = Plausible::exitPages();

$sources = Plausible::sources();
$channels = Plausible::channels();
$utm = Plausible::utm('utm_campaign');

$countries = Plausible::countries();
$regions = Plausible::regions();
$cities = Plausible::cities();

$devices = Plausible::devices();
$browsers = Plausible::browsers();
$os = Plausible::operatingSystems();

$goals = Plausible::goalConversions();
$events = Plausible::customEvents();

foreach ($pages as $row) {
    echo $row->label;             // /blog/hello-world
    echo $row->visitors();        // 90
    echo $row->metric('pageviews'); // 120
}

// Any other dimension
$rows = Plausible::breakdown(Dimension::Hostname);
$rows = Plausible::breakdown(Dimension::property('plan'));
```

### Realtime

```php
$visitors = Plausible::realtimeVisitors();  // 42
```

### Custom queries

The query builder maps onto the Stats API v2 payload:

```php
$query = Plausible::query()
    ->metrics(Metric::Visitors, Metric::Pageviews, Metric::BounceRate)
    ->dateRange(DateRange::Last7Days)
    ->dimensions(Dimension::Page)
    ->filter('is', Dimension::Country, ['BR', 'PT'])
    ->orderBy(Metric::Visitors, 'desc')
    ->limit(50);

$rows = Plausible::rows($query);   // list<StatsRow>
$raw = Plausible::run($query);     // raw API response
```

Explicit date ranges, pagination, imported data and time labels:

```php
$query = Plausible::query()
    ->metrics(Metric::Visitors)
    ->between('2026-01-01', '2026-01-31')
    ->dimensions(Dimension::TimeDay)
    ->withImports()
    ->withTimeLabels()
    ->limit(100)
    ->offset(100);
```

Logical filters go through `rawFilter()`:

```php
$query->rawFilter(['not', ['is', 'visit:source', ['Google']]]);
```

### Sites

```php
Plausible::sites();                                    // list sites
Plausible::site('example.com');                        // one site
Plausible::createSite('example.com', 'America/Sao_Paulo');
Plausible::deleteSite('example.com');
```

### Goals

```php
Plausible::goals();
Plausible::createEventGoal('Signup');
Plausible::createPageGoal('/checkout/success');
Plausible::deleteGoal(1);
```

## Available Enums

### `Metric`
`Visitors`, `Visits`, `Pageviews`, `ViewsPerVisit`, `BounceRate`, `VisitDuration`, `Events`, `ScrollDepth`, `Percentage`, `ConversionRate`, `GroupConversionRate`, `TimeOnPage`, `ExitRate`, `AverageRevenue`, `TotalRevenue`

### `DateRange`
`Day`, `Last7Days`, `Last28Days`, `Last30Days`, `Last91Days`, `Month`, `Last6Months`, `Last12Months`, `Year`, `All`

### `Dimension`
Events: `Page`, `Hostname`, `EventName`, `Goal`, `Dimension::property('key')`
Acquisition: `Source`, `Referrer`, `Channel`, `UtmSource`, `UtmMedium`, `UtmCampaign`, `UtmContent`, `UtmTerm`
Technology: `Device`, `Browser`, `BrowserVersion`, `Os`, `OsVersion`
Geography: `Country`, `CountryName`, `Region`, `RegionName`, `City`, `CityName`
Navigation: `EntryPage`, `ExitPage`
Time: `TimeMinute`, `TimeHour`, `TimeDay`, `TimeWeek`, `TimeMonth`

## Error Handling

| Exception | When |
|-----------|------|
| `AuthenticationException` | Missing/invalid API key, missing site id, or 403 from the API |
| `RateLimitException` | HTTP 429 |
| `PlausibleException` | Any other API failure (base class of both above) |

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## Static Analysis

```bash
composer analyse
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
