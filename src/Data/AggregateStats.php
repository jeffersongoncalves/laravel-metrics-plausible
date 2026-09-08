<?php

namespace JeffersonGoncalves\MetricsPlausible\Data;

class AggregateStats
{
    /**
     * @param  array<string, int|float|null>  $metrics
     */
    public function __construct(
        public readonly array $metrics = [],
    ) {}

    /**
     * @param  array<int|string, mixed>  $response
     */
    public static function fromResponse(array $response): self
    {
        $names = MetricNames::fromResponse($response);

        $results = is_array($response['results'] ?? null) ? $response['results'] : [];
        $first = is_array($results[0] ?? null) ? $results[0] : [];
        $values = is_array($first['metrics'] ?? null) ? array_values($first['metrics']) : [];

        $metrics = [];

        foreach ($names as $index => $name) {
            $metrics[$name] = self::cast($values[$index] ?? null);
        }

        return new self($metrics);
    }

    public function get(string $metric, int|float|null $default = null): int|float|null
    {
        return $this->metrics[$metric] ?? $default;
    }

    public function visitors(): int
    {
        return (int) $this->get('visitors', 0);
    }

    public function visits(): int
    {
        return (int) $this->get('visits', 0);
    }

    public function pageviews(): int
    {
        return (int) $this->get('pageviews', 0);
    }

    public function bounceRate(): float
    {
        return (float) $this->get('bounce_rate', 0);
    }

    public function visitDuration(): float
    {
        return (float) $this->get('visit_duration', 0);
    }

    /**
     * @return array<string, int|float|null>
     */
    public function toArray(): array
    {
        return $this->metrics;
    }

    public static function cast(mixed $value): int|float|null
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return str_contains((string) $value, '.') ? (float) $value : (int) $value;
        }

        return null;
    }
}
