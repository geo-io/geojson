<?php

namespace GeoIO\GeoJSON;

use GeoIO\CRS;
use GeoIO\Factory as FactoryInterface;

class Factory implements FactoryInterface
{
    public function createPoint($dimension, array $coordinates, $srid = null)
    {
        $geometry = array(
            'type' => 'Point',
            'coordinates' => $this->coordinates($coordinates)
        );

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createLineString($dimension, array $points, $srid = null)
    {
        $geometry = array(
            'type' => 'LineString',
            'coordinates' => array_map(function($point) {
                return $point['coordinates'];
            }, $points)
        );

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createLinearRing($dimension, array $points, $srid = null)
    {
        return $this->createLineString($dimension, $points, $srid);
    }

    public function createPolygon($dimension, array $lineStrings, $srid = null)
    {
        $geometry = array(
            'type' => 'Polygon',
            'coordinates' => array_map(function($lineString) {
                return $lineString['coordinates'];
            }, $lineStrings)
        );

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createMultiPoint($dimension, array $points, $srid = null)
    {
        $geometry = array(
            'type' => 'MultiPoint',
            'coordinates' => array_map(function($point) {
                return $point['coordinates'];
            }, $points)
        );

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createMultiLineString($dimension, array $lineStrings, $srid = null)
    {
        $geometry = array(
            'type' => 'MultiLineString',
            'coordinates' => array_map(function($lineString) {
                return $lineString['coordinates'];
            }, $lineStrings)
        );

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createMultiPolygon($dimension, array $polygons, $srid = null)
    {
        $geometry = array(
            'type' => 'MultiPolygon',
            'coordinates' => array_map(function($polygon) {
                return array_map(function($lineString) {
                    return $lineString['coordinates'];
                }, $polygon);
            }, $polygons)
        );

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    public function createGeometryCollection($dimension, array $geometries, $srid = null)
    {
        $geometry = array(
            'type' => 'GeometryCollection',
            'geometries' => $geometries
        );

        if (null !== $srid) {
            $geometry['crs'] = $this->crs($srid);
        }

        return $geometry;
    }

    private function coordinates(array $coordinates)
    {
        if (!isset($coordinates['x'], $coordinates['y'])) {
            return array();
        }

        $newCoordinates = array(
            $coordinates['x'],
            $coordinates['y']
        );

        if (isset($coordinates['z'])) {
            $newCoordinates[] = $coordinates['z'];
        }

        if (isset($coordinates['m'])) {
            if (!isset($newCoordinates[2])) {
                $newCoordinates[] = null;
            }

            $newCoordinates[] = $coordinates['m'];
        }

        return $newCoordinates;
    }

    private function crs($srid)
    {
        return array(
            'type' => 'name',
            'properties' => array(
                'name' => CRS\srid_to_urn($srid)
            )
        );
    }
}
