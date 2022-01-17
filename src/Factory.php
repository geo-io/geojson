<?php

declare(strict_types=1);

namespace GeoIO\GeoJSON;

use GeoIO\Coordinates;
use GeoIO\CRS;
use GeoIO\Dimension;
use GeoIO\Factory as FactoryInterface;

class Factory implements FactoryInterface
{
    public function createPoint(
        Dimension $dimension,
        ?int $srid,
        ?Coordinates $coordinates,
    ): array {
        $geometry = [
            'type' => 'Point',
            'coordinates' => $this->coordinates($coordinates),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createLineString(
        Dimension $dimension,
        ?int $srid,
        iterable $points,
    ): array {
        /**
         * @var iterable<array{coordinates: array}> $points
         */
        $geometry = [
            'type' => 'LineString',
            'coordinates' => $this->geometriesToCoordinates($points),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    /**
     * @param iterable<array{coordinates: array}> $geometries
     */
    public function createLinearRing(
        Dimension $dimension,
        ?int $srid,
        iterable $points,
    ): array {
        return $this->createLineString($dimension, $srid, $points);
    }

    public function createPolygon(
        Dimension $dimension,
        ?int $srid,
        iterable $linearRings,
    ): array {
        /**
         * @var iterable<array{coordinates: array}> $linearRings
         */
        $geometry = [
            'type' => 'Polygon',
            'coordinates' => $this->geometriesToCoordinates($linearRings),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createMultiPoint(
        Dimension $dimension,
        ?int $srid,
        iterable $points,
    ): array {
        /**
         * @var iterable<array{coordinates: array}> $points
         */
        $geometry = [
            'type' => 'MultiPoint',
            'coordinates' => $this->geometriesToCoordinates($points),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createMultiLineString(
        Dimension $dimension,
        ?int $srid,
        iterable $lineStrings,
    ): array {
        /**
         * @var iterable<array{coordinates: array}> $lineStrings
         */
        $geometry = [
            'type' => 'MultiLineString',
            'coordinates' => $this->geometriesToCoordinates($lineStrings),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createMultiPolygon(
        Dimension $dimension,
        ?int $srid,
        iterable $polygons,
    ): array {
        $coordinates = [];

        /** @var iterable<array{coordinates: array}> $polygon */
        foreach ($polygons as $polygon) {
            $coordinates[] = $this->geometriesToCoordinates($polygon);
        }

        $geometry = [
            'type' => 'MultiPolygon',
            'coordinates' => $coordinates,
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createGeometryCollection(
        Dimension $dimension,
        ?int $srid,
        iterable $geometries,
    ): array {
        $geos = [];

        /** @var array $geometry */
        foreach ($geometries as $geometry) {
            $geos[] = $geometry;
        }

        $geometry = [
            'type' => 'GeometryCollection',
            'geometries' => $geos,
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    /**
     * @param iterable<array{coordinates: array}> $geometries
     */
    private function geometriesToCoordinates(iterable $geometries): array
    {
        $coordinates = [];

        foreach ($geometries as $geometry) {
            $coordinates[] = $geometry['coordinates'];
        }

        return $coordinates;
    }

    private function coordinates(?Coordinates $coordinates): array
    {
        if (null === $coordinates) {
            return [];
        }

        $newCoordinates = [
            $coordinates->x,
            $coordinates->y,
        ];

        if (null !== $coordinates->z) {
            $newCoordinates[] = $coordinates->z;
        }

        if (null !== $coordinates->m) {
            if (!isset($newCoordinates[2])) {
                $newCoordinates[] = null;
            }

            $newCoordinates[] = $coordinates->m;
        }

        return $newCoordinates;
    }

    private function crs(int $srid): array
    {
        return [
            'type' => 'name',
            'properties' => [
                'name' => CRS\srid_to_urn($srid),
            ],
        ];
    }
}
