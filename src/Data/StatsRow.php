<?php

namespace JeffersonGoncalves\MetricsPlausible\Data;

class StatsRow
{
    /**
     * @param  array<string, string>  $dimensions
     * @param  array<string, int|float|null>  $metrics
     */
    public function __construct(
        public readonly string $label,
        public readonly array $dimensions = [],
        public readonly array $metrics = [],
    ) {}

    /**
     * @param  array<int|string, mixed>  $response
     * @return list<self>
     */
    public static function collectionFromResponse(array $response): array
    {
        $metricNames = MetricNames::fromResponse($response);
        $dimensionNames = MetricNames::dimensionsFromResponse($response);

        $results = is_array($response['results'] ?? null) ? $response['results'] : [];

        $rows = [];

        foreach ($results as $result) {
            if (is_array($result)) {
                $rows[] = self::fromResult($result, $metricNames, $dimensionNames);
            }
        }

        return $rows;
    }

    /**
     * @param  array<int|string, mixed>  $result
     * @param  list<string>  $metricNames
     * @param  list<string>  $dimensionNames
     */
    public static function fromResult(array $result, array $metricNames, array $dimensionNames): self
    {
        $metricValues = is_array($result['metrics'] ?? null) ? array_values($result['metrics']) : [];
        $dimensionValues = is_array($result['dimensions'] ?? null) ? array_values($result['dimensions']) : [];

        $metrics = [];

        foreach ($metricNames as $index => $name) {
            $metrics[$name] = AggregateStats::cast($metricValues[$index] ?? null);
        }

        $dimensions = [];

        foreach ($dimensionNames as $index => $name) {
            $dimensions[$name] = (string) ($dimensionValues[$index] ?? '');
        }

        return new self(
            label: (string) ($dimensionValues[0] ?? ''),
            dimensions: $dimensions,
            metrics: $metrics,
        );
    }

    public function metric(string $name, int|float|null $default = null): int|float|null
    {
        return $this->metrics[$name] ?? $default;
    }

    public function dimension(string $name, string $default = ''): string
    {
        return $this->dimensions[$name] ?? $default;
    }

    public function visitors(): int
    {
        return (int) $this->metric('visitors', 0);
    }

    public function pageviews(): int
    {
        return (int) $this->metric('pageviews', 0);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'dimensions' => $this->dimensions,
            'metrics' => $this->metrics,
        ];
    }
}
