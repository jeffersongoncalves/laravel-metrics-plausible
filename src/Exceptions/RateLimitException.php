<?php

namespace JeffersonGoncalves\MetricsPlausible\Exceptions;

class RateLimitException extends PlausibleException
{
    public static function exceeded(): self
    {
        return new self('Plausible API rate limit exceeded. Try again later.', 429);
    }
}
