<?php

namespace JeffersonGoncalves\MetricsPlausible;

use JeffersonGoncalves\MetricsPlausible\Data\AggregateStats;
use JeffersonGoncalves\MetricsPlausible\Data\StatsRow;
use JeffersonGoncalves\MetricsPlausible\Enums\DateRange;
use JeffersonGoncalves\MetricsPlausible\Enums\Dimension;
use JeffersonGoncalves\MetricsPlausible\Enums\GoalType;
use JeffersonGoncalves\MetricsPlausible\Enums\Metric;
use JeffersonGoncalves\MetricsPlausible\Exceptions\AuthenticationException;
use JeffersonGoncalves\MetricsPlausible\Queries\StatsQuery;
use JeffersonGoncalves\MetricsPlausible\Settings\PlausibleSettings;

class Plausible
{
    public function __construct(
        private readonly PlausibleClient $client,
    ) {}

    /**
     * Start a custom Stats API v2 query.
     */
    public function query(): StatsQuery
    {
        return StatsQuery::make();
    }

    /**
     * Run a Stats API v2 query and return the raw response.
     *
     * @return array<int|string, mixed>
     */
    public function run(StatsQuery $query): array
    {
        return $this->client->request('POST', '/api/v2/query', $query->toArray());
    }

    /**
     * Run a Stats API v2 query and return the rows.
     *
     * @return list<StatsRow>
     */
    public function rows(StatsQuery $query): array
    {
        return StatsRow::collectionFromResponse($this->run($query));
    }

    /**
     * Site-wide totals for the given period.
     *
     * @param  list<Metric|string>  $metrics
     */
    public function aggregate(
        array $metrics = [],
        DateRange|string $dateRange = DateRange::Last30Days,
        ?string $siteId = null,
    ): AggregateStats {
        $query = $this->baseQuery($dateRange, $siteId)->metrics(...($metrics === [] ? [
            Metric::Visitors,
            Metric::Visits,
            Metric::Pageviews,
            Metric::BounceRate,
            Metric::VisitDuration,
        ] : $metrics));

        return AggregateStats::fromResponse($this->run($query));
    }

    /**
     * Metrics bucketed over time.
     *
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function timeseries(
        DateRange|string $dateRange = DateRange::Last30Days,
        Dimension|string $interval = Dimension::TimeDay,
        array $metrics = [],
        ?string $siteId = null,
    ): array {
        $query = $this->baseQuery($dateRange, $siteId)
            ->metrics(...($metrics === [] ? [Metric::Visitors, Metric::Pageviews] : $metrics))
            ->dimensions($interval);

        return $this->rows($query);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function breakdown(
        Dimension|string $dimension,
        DateRange|string $dateRange = DateRange::Last30Days,
        array $metrics = [],
        int $limit = 100,
        ?string $siteId = null,
    ): array {
        $query = $this->baseQuery($dateRange, $siteId)
            ->metrics(...($metrics === [] ? [Metric::Visitors, Metric::Pageviews] : $metrics))
            ->dimensions($dimension)
            ->limit($limit);

        return $this->rows($query);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function pages(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Page, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function entryPages(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::EntryPage, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function exitPages(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::ExitPage, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function sources(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Source, $dateRange, $metrics === [] ? [Metric::Visitors, Metric::BounceRate] : $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function channels(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Channel, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function countries(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Country, $dateRange, $metrics === [] ? [Metric::Visitors, Metric::Percentage] : $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function regions(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Region, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function cities(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::City, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function devices(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Device, $dateRange, $metrics === [] ? [Metric::Visitors, Metric::Percentage] : $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function browsers(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Browser, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function operatingSystems(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Os, $dateRange, $metrics, $limit, $siteId);
    }

    /**
     * UTM breakdown. `$param` accepts `utm_source`, `utm_medium`, `utm_campaign`, `utm_content` or `utm_term`.
     *
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function utm(string $param = 'utm_source', DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown('visit:'.$param, $dateRange, $metrics === [] ? [Metric::Visitors, Metric::BounceRate] : $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function goalConversions(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::Goal, $dateRange, $metrics === [] ? [Metric::Visitors, Metric::Events, Metric::ConversionRate] : $metrics, $limit, $siteId);
    }

    /**
     * @param  list<Metric|string>  $metrics
     * @return list<StatsRow>
     */
    public function customEvents(DateRange|string $dateRange = DateRange::Last30Days, int $limit = 100, array $metrics = [], ?string $siteId = null): array
    {
        return $this->breakdown(Dimension::EventName, $dateRange, $metrics === [] ? [Metric::Visitors, Metric::Events] : $metrics, $limit, $siteId);
    }

    /**
     * Visitors on the site right now.
     */
    public function realtimeVisitors(?string $siteId = null): int
    {
        $response = $this->client->request('GET', '/api/v1/stats/realtime/visitors', [], [
            'site_id' => $this->resolveSiteId($siteId),
        ]);

        return (int) ($response['value'] ?? 0);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function sites(int $limit = 100, ?string $after = null, ?string $before = null): array
    {
        return $this->client->request('GET', '/api/v1/sites', [], array_filter([
            'limit' => $limit,
            'after' => $after,
            'before' => $before,
        ], fn ($value) => $value !== null));
    }

    /**
     * @return array<int|string, mixed>
     */
    public function site(?string $siteId = null): array
    {
        return $this->client->request('GET', '/api/v1/sites/'.urlencode($this->resolveSiteId($siteId)));
    }

    /**
     * @return array<int|string, mixed>
     */
    public function createSite(string $domain, ?string $timezone = null): array
    {
        return $this->client->request('POST', '/api/v1/sites', array_filter([
            'domain' => $domain,
            'timezone' => $timezone,
        ], fn ($value) => $value !== null));
    }

    /**
     * @return array<int|string, mixed>
     */
    public function deleteSite(?string $siteId = null): array
    {
        return $this->client->request('DELETE', '/api/v1/sites/'.urlencode($this->resolveSiteId($siteId)));
    }

    /**
     * @return array<int|string, mixed>
     */
    public function goals(?string $siteId = null): array
    {
        return $this->client->request('GET', '/api/v1/sites/goals', [], [
            'site_id' => $this->resolveSiteId($siteId),
        ]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function createEventGoal(string $eventName, ?string $siteId = null): array
    {
        return $this->client->request('PUT', '/api/v1/sites/goals', [
            'site_id' => $this->resolveSiteId($siteId),
            'goal_type' => GoalType::Event->value,
            'event_name' => $eventName,
        ]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function createPageGoal(string $pagePath, ?string $siteId = null): array
    {
        return $this->client->request('PUT', '/api/v1/sites/goals', [
            'site_id' => $this->resolveSiteId($siteId),
            'goal_type' => GoalType::Page->value,
            'page_path' => $pagePath,
        ]);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function deleteGoal(int|string $goalId, ?string $siteId = null): array
    {
        return $this->client->request('DELETE', '/api/v1/sites/goals/'.urlencode((string) $goalId), [
            'site_id' => $this->resolveSiteId($siteId),
        ]);
    }

    private function baseQuery(DateRange|string $dateRange, ?string $siteId): StatsQuery
    {
        $query = StatsQuery::make()->dateRange($dateRange);

        if ($siteId !== null) {
            $query->site($siteId);
        }

        return $query;
    }

    private function resolveSiteId(?string $siteId): string
    {
        $resolved = $siteId ?? app(PlausibleSettings::class)->site_id;

        if ($resolved === '') {
            throw AuthenticationException::missingSiteId();
        }

        return $resolved;
    }
}
