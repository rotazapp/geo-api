<?php

namespace Rotaz\GeoData\Exceptions;

class GeoDataException extends \Exception
{
    // Cus// Custom exception for GeoData-related errors

    // Example: Validation error
    public static function validationError(string $message): self
    {
        return new self("Validation Error: " . $message);
    }

    // Example: Database error
    public static function databaseError(string $message): self
    {
        return new self("Database Error: " . $message);
    }

    // Example: API error
    public static function apiError(string $message): self
    {
        return new self("API Error: " . $message);
    }

    // Example: Integration error
    public static function integrationError(string $message): self
    {
        return new self("Integration Error: " . $message);
    }

    // Example: Data conversion error
    public static function conversionError(string $message): self
    {
        return new self("Conversion Error: " . $message);
    }


}
