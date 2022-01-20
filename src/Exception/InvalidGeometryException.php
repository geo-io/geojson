<?php

declare(strict_types=1);

namespace GeoIO\GeoJSON\Exception;

use InvalidArgumentException;

class InvalidGeometryException extends InvalidArgumentException implements Exception
{
    public static function create(mixed $value, string $type = 'Geometry'): self
    {
        return new self(
            sprintf(
                'Expected valid %s object, got %s.',
                $type,
                json_encode($value, JSON_THROW_ON_ERROR),
            ),
        );
    }
}
