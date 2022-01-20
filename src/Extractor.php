<?php

declare(strict_types=1);

namespace GeoIO\GeoJSON;

use GeoIO\Coordinates;
use GeoIO\CRS;
use GeoIO\Dimension;
use GeoIO\Extractor as ExtractorInterface;
use GeoIO\GeoJSON\Exception\InvalidGeometryException;
use GeoIO\GeometryType;
use JsonSerializable;
use Throwable;
use function is_array;
use function is_object;
use function is_string;

class Extractor implements ExtractorInterface
{
    public function supports(mixed $geometry): bool
    {
        try {
            $this->extractGeometry($geometry);
        } catch (InvalidGeometryException) {
            return false;
        }

        return true;
    }

    public function extractType(mixed $geometry): GeometryType
    {
        return $this->extractGeometry($geometry)['type'];
    }

    public function extractDimension(mixed $geometry): Dimension
    {
        $geometry = $this->extractGeometry($geometry);

        if (
            GeometryType::GEOMETRYCOLLECTION === $geometry['type'] &&
            isset($geometry['geometries'][0])
        ) {
            return $this->extractDimension($geometry['geometries'][0]);
        }

        /** @var array $coordinates */
        $coordinates = match ($geometry['type']) {
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
        $geometry = $this->extractGeometry($geometry);

        /** @var array{crs: ?array{properties: array{name: ?mixed, href: ?mixed}}} $geometry */
        if (
            isset($geometry['crs']['properties']['name']) &&
            is_string($geometry['crs']['properties']['name'])
        ) {
            return CRS\def_to_srid($geometry['crs']['properties']['name']);
        }

        if (
            isset($geometry['crs']['properties']['href']) &&
            is_string($geometry['crs']['properties']['href'])
        ) {
            return CRS\def_to_srid($geometry['crs']['properties']['href']);
        }

        return null;
    }

    public function extractCoordinatesFromPoint(mixed $point): ?Coordinates
    {
        $point = $this->extractGeometry(
            $point,
            GeometryType::POINT,
        );

        if (
            !isset($point['coordinates']) ||
            !is_array($point['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $point,
                'Point',
            );
        }

        /** @var array{0: float, 1: float, 2: float|null, 3: float|null} $coordinates */
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
        $lineString = $this->extractGeometry(
            $lineString,
            GeometryType::LINESTRING,
        );

        if (
            !isset($lineString['coordinates']) ||
            !is_array($lineString['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $lineString,
                'LineString',
            );
        }

        return array_map(static function ($point) {
            return [
                'type' => 'Point',
                'coordinates' => $point,
            ];
        }, $lineString['coordinates']);
    }

    public function extractLineStringsFromPolygon(mixed $polygon): iterable
    {
        $polygon = $this->extractGeometry(
            $polygon,
            GeometryType::POLYGON,
        );

        if (
            !isset($polygon['coordinates']) ||
            !is_array($polygon['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $polygon,
                'Polygon',
            );
        }

        return array_map(static function ($lineString) {
            return [
                'type' => 'LineString',
                'coordinates' => $lineString,
            ];
        }, $polygon['coordinates']);
    }

    public function extractPointsFromMultiPoint(mixed $multiPoint): iterable
    {
        $multiPoint = $this->extractGeometry(
            $multiPoint,
            GeometryType::MULTIPOINT,
        );

        if (
            !isset($multiPoint['coordinates']) ||
            !is_array($multiPoint['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiPoint,
                'MultiPoint',
            );
        }

        return array_map(static function ($point) {
            return [
                'type' => 'Point',
                'coordinates' => $point,
            ];
        }, $multiPoint['coordinates']);
    }

    public function extractLineStringsFromMultiLineString(mixed $multiLineString): iterable
    {
        $multiLineString = $this->extractGeometry(
            $multiLineString,
            GeometryType::MULTILINESTRING,
        );

        if (
            !isset($multiLineString['coordinates']) ||
            !is_array($multiLineString['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiLineString,
                'MultiLineString',
            );
        }

        return array_map(static function ($lineString) {
            return [
                'type' => 'LineString',
                'coordinates' => $lineString,
            ];
        }, $multiLineString['coordinates']);
    }

    public function extractPolygonsFromMultiPolygon(mixed $multiPolygon): iterable
    {
        $multiPolygon = $this->extractGeometry(
            $multiPolygon,
            GeometryType::MULTIPOLYGON,
        );

        if (
            !isset($multiPolygon['coordinates']) ||
            !is_array($multiPolygon['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiPolygon,
                'MultiPolygon',
            );
        }

        return array_map(static function ($polygon) {
            return [
                'type' => 'Polygon',
                'coordinates' => $polygon,
            ];
        }, $multiPolygon['coordinates']);
    }

    public function extractGeometriesFromGeometryCollection(mixed $geometryCollection): iterable
    {
        $geometryCollection = $this->extractGeometry(
            $geometryCollection,
            GeometryType::GEOMETRYCOLLECTION,
        );

        if (
            !isset($geometryCollection['geometries']) ||
            !is_array($geometryCollection['geometries'])
        ) {
            throw InvalidGeometryException::create(
                $geometryCollection,
                'GeometryCollection',
            );
        }

        return $geometryCollection['geometries'];
    }

    /**
     * @return array|scalar|null
     */
    public function convertToArray(mixed $geometry): mixed
    {
        /** @var array|scalar|object|null $array */
        $array = $geometry;

        if (is_string($array)) {
            /** @var string|array $array */
            $array = $this->tryDecodeJson($array);
        }

        if (is_object($array)) {
            if ($array instanceof JsonSerializable) {
                /** @var array|scalar|null $array */
                $array = $array->jsonSerialize();
            } else {
                $array = get_object_vars($array);
            }
        }

        if (!is_array($array)) {
            return $array;
        }

        return array_map(
            [$this, 'convertToArray'],
            $array,
        );
    }

    /**
     * @return array{type: GeometryType}
     */
    private function extractGeometry(
        mixed $geometry,
        ?GeometryType $expected = null,
    ): array {
        $geometry = $this->convertToArray($geometry);

        if ('feature' === strtolower((string) ($geometry['type'] ?? ''))) {
            return $this->extractGeometry($geometry['geometry'] ?? []);
        }

        if (!is_array($geometry)) {
            throw InvalidGeometryException::create(
                $geometry,
                null !== $expected ? $expected->value : 'Geometry',
            );
        }

        $type = match (strtolower((string) ($geometry['type'] ?? ''))) {
            'point' => GeometryType::POINT,
            'linestring' => GeometryType::LINESTRING,
            'polygon' => GeometryType::POLYGON,
            'multipoint' => GeometryType::MULTIPOINT,
            'multilinestring' => GeometryType::MULTILINESTRING,
            'multipolygon' => GeometryType::MULTIPOLYGON,
            'geometrycollection' => GeometryType::GEOMETRYCOLLECTION,
            default => throw InvalidGeometryException::create(
                $geometry,
                null !== $expected ? $expected->value : 'Geometry',
            ),
        };

        if (null !== $expected && $type !== $expected) {
            throw InvalidGeometryException::create(
                $geometry,
                $expected->value,
            );
        }

        return [
            'type' => $type,
            'coordinates' => $geometry['coordinates'] ?? null,
            'geometries' => $geometry['geometries'] ?? null,
            'crs' => $geometry['crs'] ?? null,
        ];
    }

    private function tryDecodeJson(string $str): mixed
    {
        if ('{' !== trim($str[0])) {
            return $str;
        }

        $data = $str;

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($str, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return $str;
        }

        if (is_array($decoded)) {
            $data = $decoded;
        }

        return $data;
    }
}
