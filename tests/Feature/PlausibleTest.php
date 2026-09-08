<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\MetricsPlausible\Data\AggregateStats;
use JeffersonGoncalves\MetricsPlausible\Enums\DateRange;
use JeffersonGoncalves\MetricsPlausible\Exceptions\AuthenticationException;
use JeffersonGoncalves\MetricsPlausible\Exceptions\PlausibleException;
use JeffersonGoncalves\MetricsPlausible\Exceptions\RateLimitException;
use JeffersonGoncalves\MetricsPlausible\Facades\Plausible;

function fakeQuery(array $metrics, array $results, array $dimensions = []): void
{
    Http::fake([
        'plausible.io/api/v2/query' => Http::response([
            'results' => $results,
            'meta' => [],
            'query' => ['metrics' => $metrics, 'dimensions' => $dimensions],
        ]),
    ]);
}

it('maps positional metrics onto their names', function (): void {
    fakeQuery(
        ['visitors', 'pageviews', 'bounce_rate'],
        [['metrics' => [120, 340, 41.5], 'dimensions' => []]],
    );

    $stats = Plausible::aggregate();

    expect($stats)->toBeInstanceOf(AggregateStats::class)
        ->and($stats->visitors())->toBe(120)
        ->and($stats->pageviews())->toBe(340)
        ->and($stats->bounceRate())->toBe(41.5)
        ->and($stats->get('missing'))->toBeNull();
});

it('sends the configured site id and date range', function (): void {
    fakeQuery(['visitors'], []);

    Plausible::aggregate(['visitors'], DateRange::Last7Days);

    Http::assertSent(function ($request): bool {
        expect($request['site_id'])->toBe('example.com')
            ->and($request['metrics'])->toBe(['visitors'])
            ->and($request['date_range'])->toBe('7d');

        return true;
    });
});

it('builds rows out of a dimensional breakdown', function (): void {
    fakeQuery(
        ['visitors', 'pageviews'],
        [
            ['metrics' => [90, 120], 'dimensions' => ['/blog']],
            ['metrics' => [30, 45], 'dimensions' => ['/pricing']],
        ],
        ['event:page'],
    );

    $rows = Plausible::pages();

    expect($rows)->toHaveCount(2)
        ->and($rows[0]->label)->toBe('/blog')
        ->and($rows[0]->visitors())->toBe(90)
        ->and($rows[0]->pageviews())->toBe(120)
        ->and($rows[0]->dimension('event:page'))->toBe('/blog')
        ->and($rows[1]->label)->toBe('/pricing');
});

it('reads the bare integer returned by the realtime endpoint', function (): void {
    Http::fake([
        'plausible.io/api/v1/stats/realtime/visitors*' => Http::response('42', 200, ['Content-Type' => 'application/json']),
    ]);

    expect(Plausible::realtimeVisitors())->toBe(42);
});

it('creates an event goal', function (): void {
    Http::fake([
        'plausible.io/api/v1/sites/goals' => Http::response(['id' => 1, 'event_name' => 'Signup']),
    ]);

    expect(Plausible::createEventGoal('Signup'))->toBe(['id' => 1, 'event_name' => 'Signup']);

    Http::assertSent(fn ($request): bool => $request['goal_type'] === 'event'
        && $request['event_name'] === 'Signup'
        && $request['site_id'] === 'example.com');
});

it('translates api failures into typed exceptions', function (int $status, string $exception): void {
    Http::fake([
        'plausible.io/api/v2/query' => Http::response(['error' => 'nope'], $status),
    ]);

    expect(fn () => Plausible::aggregate())->toThrow($exception);
})->with([
    [401, AuthenticationException::class],
    [403, AuthenticationException::class],
    [429, RateLimitException::class],
    [500, PlausibleException::class],
]);
