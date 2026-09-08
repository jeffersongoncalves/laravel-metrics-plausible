<?php

namespace JeffersonGoncalves\MetricsPlausible\Exceptions;

use Exception;

class PlausibleException extends Exception
{
    public static function fromResponse(int $statusCode, string $message): self
    {
        return new self("Plausible API error ({$statusCode}): {$message}", $statusCode);
    }

    public static function apiError(string $message): self
    {
        return new self("Plausible API error: {$message}");
    }
}
