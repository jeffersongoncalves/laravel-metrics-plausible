<?php

namespace JeffersonGoncalves\MetricsPlausible\Queries;

use DateTimeInterface;
use JeffersonGoncalves\MetricsPlausible\Enums\DateRange;
use JeffersonGoncalves\MetricsPlausible\Enums\Dimension;
use JeffersonGoncalves\MetricsPlausible\Enums\Metric;
use JeffersonGoncalves\MetricsPlausible\Exceptions\AuthenticationException;
use JeffersonGoncalves\MetricsPlausible\Settings\PlausibleSettings;

class StatsQuery
{
    private ?string $siteId = null;

    /** @var list<string> */
    private array $metrics = [];

    /** @var string|list<string> */
    private string|array $dateRange = '30d';

    /** @var list<string> */
    private array $dimensions = [];

    /** @var list<array<int, mixed>> */
    private array $filters = [];

    /** @var list<array{0: string, 1: string}> */
    private array $orderBy = [];

    private ?int $limit = null;

    private ?int $offset = null;

    /** @var array<string, mixed> */
    private array $include = [];

    public static function make(): self
    {
        return new self;
    }

    public function site(string $siteId): self
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function metrics(Metric|string ...$metrics): self
    {
        foreach ($metrics as $metric) {
            $this->metrics[] = $metric instanceof Metric ? $metric->value : $metric;
        }

        return $this;
    }

    public function dateRange(DateRange|string $range): self
    {
        $this->dateRange = $range instanceof DateRange ? $range->value : $range;

        return $this;
    }

    public function between(DateTimeInterface|string $from, DateTimeInterface|string $to): self
    {
        $this->dateRange = [
            $from instanceof DateTimeInterface ? $from->format('Y-m-d') : $from,
            $to instanceof DateTimeInterface ? $to->format('Y-m-d') : $to,
        ];

        return $this;
    }

    public function dimensions(Dimension|string ...$dimensions): self
    {
        foreach ($dimensions as $dimension) {
            $this->dimensions[] = $dimension instanceof Dimension ? $dimension->value : $dimension;
        }

        return $this;
    }

    /**
     * @param  array<int|string, mixed>|string|int|float  $values
     */
    public function filter(string $operator, Dimension|string $dimension, array|string|int|float $values = []): self
    {
        $this->filters[] = [
            $operator,
            $dimension instanceof Dimension ? $dimension->value : $dimension,
            is_array($values) ? array_values($values) : [$values],
        ];

        return $this;
    }

    /**
     * Add a raw filter clause, for logical operators such as `and`, `or` and `not`.
     *
     * @param  array<int, mixed>  $clause
     */
    public function rawFilter(array $clause): self
    {
        $this->filters[] = $clause;

        return $this;
    }

    public function orderBy(Metric|Dimension|string $field, string $direction = 'desc'): self
    {
        $value = $field instanceof Metric || $field instanceof Dimension ? $field->value : $field;

        $this->orderBy[] = [$value, $direction];

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function include(string $key, mixed $value = true): self
    {
        $this->include[$key] = $value;

        return $this;
    }

    public function withImports(bool $enabled = true): self
    {
        return $this->include('imports', $enabled);
    }

    public function withTimeLabels(bool $enabled = true): self
    {
        return $this->include('time_labels', $enabled);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $siteId = $this->siteId ?? app(PlausibleSettings::class)->site_id;

        if ($siteId === '') {
            throw AuthenticationException::missingSiteId();
        }

        $payload = [
            'site_id' => $siteId,
            'metrics' => $this->metrics === [] ? [Metric::Visitors->value] : $this->metrics,
            'date_range' => $this->dateRange,
        ];

        if ($this->dimensions !== []) {
            $payload['dimensions'] = $this->dimensions;
        }

        if ($this->filters !== []) {
            $payload['filters'] = $this->filters;
        }

        if ($this->orderBy !== []) {
            $payload['order_by'] = $this->orderBy;
        }

        if ($this->limit !== null || $this->offset !== null) {
            $payload['pagination'] = array_filter([
                'limit' => $this->limit,
                'offset' => $this->offset,
            ], fn ($value) => $value !== null);
        }

        if ($this->include !== []) {
            $payload['include'] = $this->include;
        }

        return $payload;
    }
}
