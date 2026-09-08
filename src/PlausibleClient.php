<?php

namespace JeffersonGoncalves\MetricsPlausible;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\MetricsPlausible\Exceptions\AuthenticationException;
use JeffersonGoncalves\MetricsPlausible\Exceptions\PlausibleException;
use JeffersonGoncalves\MetricsPlausible\Exceptions\RateLimitException;

class PlausibleClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl = 'https://plausible.io',
    ) {}

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $query
     * @return array<int|string, mixed>
     */
    public function request(string $method, string $path, array $body = [], array $query = []): array
    {
        $this->ensureApiKey();

        $url = rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');

        $request = $this->buildRequest();

        $response = match (strtoupper($method)) {
            'GET' => $request->get($url, $query),
            'POST' => $request->post($url, $body),
            'PUT' => $request->put($url, $body),
            'DELETE' => $request->delete($url, $body),
            default => throw PlausibleException::apiError("Unsupported HTTP method [{$method}]."),
        };

        return $this->handleResponse($response);
    }

    private function buildRequest(): PendingRequest
    {
        return Http::withToken($this->apiKey)
            ->acceptJson()
            ->asJson();
    }

    private function ensureApiKey(): void
    {
        if ($this->apiKey === '') {
            throw AuthenticationException::missingApiKey();
        }
    }

    /**
     * @return array<int|string, mixed>
     */
    private function handleResponse(Response $response): array
    {
        if ($response->status() === 401) {
            throw AuthenticationException::invalidApiKey();
        }

        if ($response->status() === 403) {
            throw AuthenticationException::forbidden();
        }

        if ($response->status() === 429) {
            throw RateLimitException::exceeded();
        }

        if (! $response->successful()) {
            throw PlausibleException::fromResponse($response->status(), $this->errorMessage($response));
        }

        $data = $response->json();

        // The realtime endpoint answers with a bare integer.
        if (is_int($data)) {
            return ['value' => $data];
        }

        return is_array($data) ? $data : [];
    }

    private function errorMessage(Response $response): string
    {
        $data = $response->json();

        if (is_array($data)) {
            if (isset($data['error']) && is_string($data['error'])) {
                return $data['error'];
            }

            if (isset($data['errors']) && is_array($data['errors'])) {
                return json_encode($data['errors']) ?: $response->body();
            }
        }

        return $response->body();
    }
}
