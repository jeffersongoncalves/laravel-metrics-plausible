<?php

namespace JeffersonGoncalves\MetricsPlausible\Data;

/**
 * Plausible answers with positional metric values, the names live in `query.metrics`.
 *
 * @internal
 */
class MetricNames
{
    /**
     * @param  array<int|string, mixed>  $response
     * @return list<string>
     */
    public static function fromResponse(array $response): array
    {
        return self::stringList($response, 'metrics');
    }

    /**
     * @param  array<int|string, mixed>  $response
     * @return list<string>
     */
    public static function dimensionsFromResponse(array $response): array
    {
        return self::stringList($response, 'dimensions');
    }

    /**
     * @param  array<int|string, mixed>  $response
     * @return list<string>
     */
    private static function stringList(array $response, string $key): array
    {
        $query = is_array($response['query'] ?? null) ? $response['query'] : [];
        $values = is_array($query[$key] ?? null) ? $query[$key] : [];

        return array_values(array_map(static fn ($value): string => (string) $value, $values));
    }
}
