<?php

namespace GeoIO\GeoJSON;

use GeoIO\Coordinates;
use GeoIO\CRS;
use GeoIO\Dimension;
use GeoIO\Extractor as ExtractorInterface;
use GeoIO\GeoJSON\Exception\InvalidGeometryException;
use GeoIO\GeometryType;
use JsonSerializable;
use Throwable;

class Extractor implements ExtractorInterface
{
    public function supports(mixed $geometry): bool
    {
        $geometry = $this->convertToArray($geometry);

        if (!is_array($geometry)) {
            return false;
        }

        if (!isset($geometry['type'])) {
            return false;
        }

        return true;
    }

    public function extractType(mixed $geometry): string
    {
        $geometry = $this->convertToArray($geometry);

        if (
            !is_array($geometry) ||
            !isset($geometry['type'])
        ) {
            throw InvalidGeometryException::create(
                $geometry
            );
        }

        return match (strtolower($geometry['type'])) {
            'point' => GeometryType::POINT,
            'linestring' => GeometryType::LINESTRING,
            'polygon' => GeometryType::POLYGON,
            'multipoint' => GeometryType::MULTIPOINT,
            'multilinestring' => GeometryType::MULTILINESTRING,
            'multipolygon' => GeometryType::MULTIPOLYGON,
            'geometrycollection' => GeometryType::GEOMETRYCOLLECTION,
            default => throw InvalidGeometryException::create($geometry),
        };
    }

    public function extractDimension(mixed $geometry): string
    {
        $geometry = $this->convertToArray($geometry);

        $type = $this->extractType($geometry);

        if (
            GeometryType::GEOMETRYCOLLECTION === $type &&
            isset($geometry['geometries'][0])
        ) {
            return $this->extractDimension($geometry['geometries'][0]);
        }

        $coordinates = match ($type) {
            GeometryType::POINT => $geometry['coordinates'] ?? [],
            GeometryType::LINESTRING => $geometry['coordinates'][0] ?? [],
            GeometryType::POLYGON => $geometry['coordinates'][0][0] ?? [],
            GeometryType::MULTIPOINT => $geometry['coordinates'][0] ?? [],
            GeometryType::MULTILINESTRING => $geometry['coordinates'][0][0] ?? [],
            GeometryType::MULTIPOLYGON => $geometry['coordinates'][0][0][0] ?? [],
            GeometryType::GEOMETRYCOLLECTION => [],
        };

        if (isset($coordinates[2], $coordinates[3])) {
            return Dimension::DIMENSION_4D;
        }

        if (isset($coordinates[2])) {
            return Dimension::DIMENSION_3DZ;
        }

        if (isset($coordinates[3])) {
            return Dimension::DIMENSION_3DM;
        }

        return Dimension::DIMENSION_2D;
    }

    public function extractSrid(mixed $geometry): ?int
    {
        $geometry = $this->convertToArray($geometry);

        if (!is_array($geometry)) {
            return null;
        }

        if (isset($geometry['crs']['properties']['name'])) {
            return CRS\def_to_srid($geometry['crs']['properties']['name']);
        }

        if (isset($geometry['crs']['properties']['href'])) {
            return CRS\def_to_srid($geometry['crs']['properties']['href']);
        }

        return null;
    }

    public function extractCoordinatesFromPoint(mixed $point): ?Coordinates
    {
        $point = $this->convertToArray($point);

        if (
            !isset($point['coordinates']) ||
            !is_array($point['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $point,
                'Point'
            );
        }

        $coordinates = $point['coordinates'];

        if (
            !isset($coordinates[0], $coordinates[1])
        ) {
            return null;
        }

        return new Coordinates(
            x: $coordinates[0],
            y: $coordinates[1],
            z: $coordinates[2] ?? null,
            m: $coordinates[3] ?? null,
        );
    }

    public function extractPointsFromLineString(mixed $lineString): iterable
    {
        $lineString = $this->convertToArray($lineString);

        if (
            !isset($lineString['coordinates']) ||
            !is_array($lineString['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $lineString,
                'LineString'
            );
        }

        return array_map(function ($point) {
            return [
                'type' => 'Point',
                'coordinates' => $point,
            ];
        }, $lineString['coordinates']);
    }

    public function extractLineStringsFromPolygon(mixed $polygon): iterable
    {
        $polygon = $this->convertToArray($polygon);

        if (
            !isset($polygon['coordinates']) ||
            !is_array($polygon['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $polygon,
                'Polygon'
            );
        }

        return array_map(function ($lineString) {
            return [
                'type' => 'LineString',
                'coordinates' => $lineString,
            ];
        }, $polygon['coordinates']);
    }

    public function extractPointsFromMultiPoint(mixed $multiPoint): iterable
    {
        $multiPoint = $this->convertToArray($multiPoint);

        if (
            !isset($multiPoint['coordinates']) ||
            !is_array($multiPoint['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiPoint,
                'MultiPoint'
            );
        }

        return array_map(function ($point) {
            return [
                'type' => 'Point',
                'coordinates' => $point,
            ];
        }, $multiPoint['coordinates']);
    }

    public function extractLineStringsFromMultiLineString(mixed $multiLineString): iterable
    {
        $multiLineString = $this->convertToArray($multiLineString);

        if (
            !isset($multiLineString['coordinates']) ||
            !is_array($multiLineString['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiLineString,
                'MultiLineString'
            );
        }

        return array_map(function ($lineString) {
            return [
                'type' => 'LineString',
                'coordinates' => $lineString,
            ];
        }, $multiLineString['coordinates']);
    }

    public function extractPolygonsFromMultiPolygon(mixed $multiPolygon): iterable
    {
        $multiPolygon = $this->convertToArray($multiPolygon);

        if (
            !isset($multiPolygon['coordinates']) ||
            !is_array($multiPolygon['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiPolygon,
                'MultiPolygon'
            );
        }

        return array_map(function ($polygon) {
            return [
                'type' => 'Polygon',
                'coordinates' => $polygon,
            ];
        }, $multiPolygon['coordinates']);
    }

    public function extractGeometriesFromGeometryCollection(mixed $geometryCollection): iterable
    {
        $geometryCollection = $this->convertToArray($geometryCollection);

        if (
            !isset($geometryCollection['geometries']) ||
            !is_array($geometryCollection['geometries'])
        ) {
            throw InvalidGeometryException::create(
                $geometryCollection,
                'GeometryCollection'
            );
        }

        return $geometryCollection['geometries'];
    }

    public function convertToArray(mixed $geometry): mixed
    {
        $array = $geometry;

        if (is_string($array)) {
            try {
                return json_decode($array, true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                return $array;
            }
        }

        if (is_object($array)) {
            $array = get_object_vars($array);
        }

        if (!is_array($array)) {
            return $array;
        }

        return array_map(
            [$this, 'convertToArray'],
            $array
        );
    }
}
