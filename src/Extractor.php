<?php

namespace GeoIO\GeoJSON;

use GeoIO\CRS;
use GeoIO\Dimension;
use GeoIO\Extractor as ExtractorInterface;
use GeoIO\GeoJSON\Exception\InvalidGeometryException;

class Extractor implements ExtractorInterface
{
    public function supports($geometry)
    {
        $geometry = $this->tryConvertToArray($geometry);

        if (!is_array($geometry)) {
            return false;
        }

        if (!isset($geometry['type'])) {
            return false;
        }

        return true;
    }

    public function extractType($geometry)
    {
        $geometry = $this->tryConvertToArray($geometry);

        if (
            !is_array($geometry) ||
            !isset($geometry['type'])
        ) {
            throw InvalidGeometryException::create(
                $geometry
            );
        }

        switch (strtolower($geometry['type'])) {
            case 'point':
                return ExtractorInterface::TYPE_POINT;
            case 'linestring':
                return ExtractorInterface::TYPE_LINESTRING;
            case 'polygon':
                return ExtractorInterface::TYPE_POLYGON;
            case 'multipoint':
                return ExtractorInterface::TYPE_MULTIPOINT;
            case 'multilinestring':
                return ExtractorInterface::TYPE_MULTILINESTRING;
            case 'multipolygon':
                return ExtractorInterface::TYPE_MULTIPOLYGON;
            case 'geometrycollection':
                return ExtractorInterface::TYPE_GEOMETRYCOLLECTION;
            default:
                throw InvalidGeometryException::create($geometry);
        }
    }

    public function extractDimension($geometry)
    {
        $geometry = $this->tryConvertToArray($geometry);

        switch ($this->extractType($geometry)) {
            case ExtractorInterface::TYPE_POINT:
                if (!isset($geometry['coordinates'][0])) {
                    return Dimension::DIMENSION_2D;
                }

                $coordinates = $geometry['coordinates'];
                break;
            case ExtractorInterface::TYPE_LINESTRING:
                if (!isset($geometry['coordinates'][0])) {
                    return Dimension::DIMENSION_2D;
                }

                $coordinates = $geometry['coordinates'][0];
                break;
            case ExtractorInterface::TYPE_POLYGON:
                if (!isset($geometry['coordinates'][0][0])) {
                    return Dimension::DIMENSION_2D;
                }

                $coordinates = $geometry['coordinates'][0][0];
                break;
            case ExtractorInterface::TYPE_MULTIPOINT:
                if (!isset($geometry['coordinates'][0])) {
                    return Dimension::DIMENSION_2D;
                }

                $coordinates = $geometry['coordinates'][0];
                break;
            case ExtractorInterface::TYPE_MULTILINESTRING:
                if (!isset($geometry['coordinates'][0][0])) {
                    return Dimension::DIMENSION_2D;
                }

                $coordinates = $geometry['coordinates'][0][0];
                break;
            case ExtractorInterface::TYPE_MULTIPOLYGON:
                if (!isset($geometry['coordinates'][0][0][0])) {
                    return Dimension::DIMENSION_2D;
                }

                $coordinates = $geometry['coordinates'][0][0][0];
                break;
            case ExtractorInterface::TYPE_GEOMETRYCOLLECTION:
                if (!isset($geometry['geometries'][0])) {
                    return Dimension::DIMENSION_2D;
                }

                return $this->extractDimension($geometry['geometries'][0]);
        }

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

    public function extractSrid($geometry)
    {
        $geometry = $this->tryConvertToArray($geometry);

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

    public function extractCoordinatesFromPoint($point)
    {
        $point = $this->tryConvertToArray($point);

        if (
            !is_array($point) ||
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
            return array();
        }

        return array(
            'x' => $coordinates[0],
            'y' => $coordinates[1],
            'z' => isset($coordinates[2]) ? $coordinates[2] : null,
            'm' => isset($coordinates[3]) ? $coordinates[3] : null,
        );
    }

    public function extractPointsFromLineString($lineString)
    {
        $lineString = $this->tryConvertToArray($lineString);

        if (
            !is_array($lineString) ||
            !isset($lineString['coordinates']) ||
            !is_array($lineString['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $lineString,
                'LineString'
            );
        }

        return array_map(function ($point) {
            return array(
                'type' => 'Point',
                'coordinates' => $point
            );
        }, $lineString['coordinates']);
    }

    public function extractLineStringsFromPolygon($polygon)
    {
        $polygon = $this->tryConvertToArray($polygon);

        if (
            !is_array($polygon) ||
            !isset($polygon['coordinates']) ||
            !is_array($polygon['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $polygon,
                'Polygon'
            );
        }

        return array_map(function ($lineString) {
            return array(
                'type' => 'LineString',
                'coordinates' => $lineString
            );
        }, $polygon['coordinates']);
    }

    public function extractPointsFromMultiPoint($multiPoint)
    {
        $multiPoint = $this->tryConvertToArray($multiPoint);

        if (
            !is_array($multiPoint) ||
            !isset($multiPoint['coordinates']) ||
            !is_array($multiPoint['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiPoint,
                'MultiPoint'
            );
        }

        return array_map(function ($point) {
            return array(
                'type' => 'Point',
                'coordinates' => $point
            );
        }, $multiPoint['coordinates']);
    }

    public function extractLineStringsFromMultiLineString($multiLineString)
    {
        $multiLineString = $this->tryConvertToArray($multiLineString);

        if (
            !is_array($multiLineString) ||
            !isset($multiLineString['coordinates']) ||
            !is_array($multiLineString['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiLineString,
                'MultiLineString'
            );
        }

        return array_map(function ($lineString) {
            return array(
                'type' => 'LineString',
                'coordinates' => $lineString
            );
        }, $multiLineString['coordinates']);
    }

    public function extractPolygonsFromMultiPolygon($multiPolygon)
    {
        $multiPolygon = $this->tryConvertToArray($multiPolygon);

        if (
            !is_array($multiPolygon) ||
            !isset($multiPolygon['coordinates']) ||
            !is_array($multiPolygon['coordinates'])
        ) {
            throw InvalidGeometryException::create(
                $multiPolygon,
                'MultiPolygon'
            );
        }

        return array_map(function ($polygon) {
            return array(
                'type' => 'Polygon',
                'coordinates' => $polygon
            );
        }, $multiPolygon['coordinates']);
    }

    public function extractGeometriesFromGeometryCollection($geometryCollection)
    {
        $geometryCollection = $this->tryConvertToArray($geometryCollection);

        if (
            !is_array($geometryCollection) ||
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

    public function tryConvertToArray($geometry)
    {
        $array = $geometry;

        if (is_string($array)) {
            $decoded = json_decode($array, true);

            if (JSON_ERROR_NONE === json_last_error()) {
                $array = $decoded;
            }
        }

        if (is_object($array)) {
            $array = get_object_vars($array);
        }

        if (!is_array($array)) {
            return $geometry;
        }

        return array_map(
            array($this, 'tryConvertToArray'),
            $array
        );
    }
}
