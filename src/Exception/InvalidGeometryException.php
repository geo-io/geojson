<?php

namespace GeoIO\GeoJSON\Exception;

class InvalidGeometryException extends \InvalidArgumentException implements Exception
{
    public static function create($value, $type = 'Geometry')
    {
        return new self(
            sprintf(
                'Expected valid %s object, got %s.',
                $type,
                json_encode($value)
            )
        );
    }
}
