<?php

namespace JeffersonGoncalves\MetricsPlausible\Exceptions;

class AuthenticationException extends PlausibleException
{
    public static function missingApiKey(): self
    {
        return new self('Plausible API key is not configured. Set PLAUSIBLE_API_KEY in your .env file.');
    }

    public static function invalidApiKey(): self
    {
        return new self('The provided Plausible API key is invalid.', 401);
    }

    public static function forbidden(): self
    {
        return new self('The Plausible API key does not have access to this resource.', 403);
    }

    public static function missingSiteId(): self
    {
        return new self('Plausible site id is not configured. Set PLAUSIBLE_SITE_ID in your .env file.');
    }
}
