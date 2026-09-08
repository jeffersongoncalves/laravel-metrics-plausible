<?php

namespace JeffersonGoncalves\MetricsPlausible\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\MetricsPlausible\Data\AggregateStats;
use JeffersonGoncalves\MetricsPlausible\Data\StatsRow;
use JeffersonGoncalves\MetricsPlausible\Enums\DateRange;
use JeffersonGoncalves\MetricsPlausible\Enums\Dimension;
use JeffersonGoncalves\MetricsPlausible\Enums\Metric;
use JeffersonGoncalves\MetricsPlausible\Queries\StatsQuery;

/**
 * @method static StatsQuery query()
 * @method static array<int|string, mixed> run(StatsQuery $query)
 * @method static list<StatsRow> rows(StatsQuery $query)
 * @method static AggregateStats aggregate(array<int, Metric|string> $metrics = [], DateRange|string $dateRange = DateRange::Last30Days, ?string $siteId = null)
 * @method static list<StatsRow> timeseries(DateRange|string $dateRange = DateRange::Last30Days, Dimension|string $interval = Dimension::TimeDay, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> breakdown(Dimension|string $dimension, DateRange|string $dateRange = DateRange::Last30Days, array<int, Metric|string> $metrics = [], int $limit = 100, ?string $siteId = null)
 * @method static list<StatsRow> pages(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> entryPages(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> exitPages(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> sources(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> channels(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> countries(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> regions(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> cities(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> devices(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> browsers(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> operatingSystems(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> utm(string $param = 'utm_source', DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> goalConversions(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static list<StatsRow> customEvents(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array<int, Metric|string> $metrics = [], ?string $siteId = null)
 * @method static int realtimeVisitors(?string $siteId = null)
 * @method static array<int|string, mixed> sites(int $limit = 100, ?string $after = null, ?string $before = null)
 * @method static array<int|string, mixed> site(?string $siteId = null)
 * @method static array<int|string, mixed> createSite(string $domain, ?string $timezone = null)
 * @method static array<int|string, mixed> deleteSite(?string $siteId = null)
 * @method static array<int|string, mixed> goals(?string $siteId = null)
 * @method static array<int|string, mixed> createEventGoal(string $eventName, ?string $siteId = null)
 * @method static array<int|string, mixed> createPageGoal(string $pagePath, ?string $siteId = null)
 * @method static array<int|string, mixed> deleteGoal(int|string $goalId, ?string $siteId = null)
 *
 * @see \JeffersonGoncalves\MetricsPlausible\Plausible
 */
class Plausible extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'metrics-plausible';
    }
}
