<?php

namespace GeoIO\GeoJSON;

use GeoIO\Coordinates;
use GeoIO\CRS;
use GeoIO\Dimension;
use GeoIO\Factory as FactoryInterface;

class Factory implements FactoryInterface
{
    public function createPoint(
        string $dimension,
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
        string $dimension,
        ?int $srid,
        iterable $points,
    ): array {
        $geometry = [
            'type' => 'LineString',
            'coordinates' => $this->geometriesToCoordinates($points),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createLinearRing(
        string $dimension,
        ?int $srid,
        iterable $points,
    ): array {
        return $this->createLineString($dimension, $srid, $points);
    }

    public function createPolygon(
        string $dimension,
        ?int $srid,
        iterable $lineStrings,
    ): array {
        $geometry = [
            'type' => 'Polygon',
            'coordinates' => $this->geometriesToCoordinates($lineStrings),
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createMultiPoint(
        string $dimension,
        ?int $srid,
        iterable $points,
    ): array {
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
        string $dimension,
        ?int $srid,
        iterable $lineStrings,
    ): array {
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
        string $dimension,
        ?int $srid,
        iterable $polygons,
    ): array {
        $coordinates = [];

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
        string $dimension,
        ?int $srid,
        iterable $geometries,
    ): array {
        $geometry = [
            'type' => 'GeometryCollection',
            'geometries' => $geometries,
        ];

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

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
