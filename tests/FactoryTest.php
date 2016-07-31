<?php

namespace GeoIO\GeoJSON;

use GeoIO\Dimension;

class FactoryTest  extends \PHPUnit_Framework_TestCase
{
    public function testCreatePoint()
    {
        $factory = new Factory();

        $geometry = $factory->createPoint(
            Dimension::DIMENSION_4D, 
            array(
                'x' => 1,
                'y' => 2,
                'z' => 3,
                'm' => 4,
            ),
            4326
        );

        $expected = array(
            'type' => 'Point',
            'coordinates' => array(1, 2, 3, 4),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateEmptyPoint()
    {
        $factory = new Factory();

        $geometry = $factory->createPoint(
            Dimension::DIMENSION_4D,
            array(),
            4326
        );

        $expected = array(
            'type' => 'Point',
            'coordinates' => array(),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreatePointM()
    {
        $factory = new Factory();

        $geometry = $factory->createPoint(
            Dimension::DIMENSION_4D,
            array(
                'x' => 1,
                'y' => 2,
                'z' => null,
                'm' => 4,
            ),
            4326
        );

        $expected = array(
            'type' => 'Point',
            'coordinates' => array(1, 2, null, 4),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateLineString()
    {
        $factory = new Factory();

        $geometry = $factory->createLineString(Dimension::DIMENSION_4D, array(
            array(
                'type' => 'Point',
                'coordinates' => array(1, 1),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            ),
            array(
                'type' => 'Point',
                'coordinates' => array(2, 2),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            )
        ), 4326);

        $expected = array(
            'type' => 'LineString',
            'coordinates' => array(
                array(1, 1),
                array(2, 2)
            ),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateLinearRing()
    {
        $factory = new Factory();

        $geometry = $factory->createLinearRing(Dimension::DIMENSION_4D, array(
            array(
                'type' => 'Point',
                'coordinates' => array(1, 1),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            ),
            array(
                'type' => 'Point',
                'coordinates' => array(2, 2),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            )
        ), 4326);

        $expected = array(
            'type' => 'LineString',
            'coordinates' => array(
                array(1, 1),
                array(2, 2)
            ),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreatePolygon()
    {
        $factory = new Factory();

        $geometry = $factory->createPolygon(Dimension::DIMENSION_4D, array(
            array(
                'type' => 'LineString',
                'coordinates' => array(
                    array(1, 1),
                    array(2, 2)
                ),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            ),
            array(
                'type' => 'LineString',
                'coordinates' => array(
                    array(3, 3),
                    array(4, 4)
                ),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            )
        ), 4326);

        $expected = array(
            'type' => 'Polygon',
            'coordinates' => array(
                array(array(1, 1), array(2, 2)),
                array(array(3, 3), array(4, 4))
            ),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateMultiPoint()
    {
        $factory = new Factory();

        $geometry = $factory->createMultiPoint(Dimension::DIMENSION_4D, array(
            array(
                'type' => 'Point',
                'coordinates' => array(1, 1),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            ),
            array(
                'type' => 'Point',
                'coordinates' => array(2, 2),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            )
        ), 4326);

        $expected = array(
            'type' => 'MultiPoint',
            'coordinates' => array(
                array(1, 1),
                array(2, 2)
            ),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateMultiLineString()
    {
        $factory = new Factory();

        $geometry = $factory->createMultiLineString(Dimension::DIMENSION_4D, array(
            array(
                'type' => 'LineString',
                'coordinates' => array(
                    array(1, 1),
                    array(2, 2)
                ),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            ),
            array(
                'type' => 'LineString',
                'coordinates' => array(
                    array(3, 3),
                    array(4, 4)
                ),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            )
        ), 4326);

        $expected = array(
            'type' => 'MultiLineString',
            'coordinates' => array(
                array(array(1, 1), array(2, 2)),
                array(array(3, 3), array(4, 4))
            ),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateMultiPolygon()
    {
        $factory = new Factory();

        $geometry = $factory->createMultiPolygon(Dimension::DIMENSION_4D, array(
            array(
                array(
                    'type' => 'LineString',
                    'coordinates' => array(
                        array(1, 1),
                        array(2, 2)
                    ),
                    'crs' => array(
                        'type' => 'name',
                        'properties' => array(
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                        )
                    )
                ),
                array(
                    'type' => 'LineString',
                    'coordinates' => array(
                        array(3, 3),
                        array(4, 4)
                    ),
                    'crs' => array(
                        'type' => 'name',
                        'properties' => array(
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                        )
                    )
                )
            ),
            array(
                array(
                    'type' => 'LineString',
                    'coordinates' => array(
                        array(5, 5),
                        array(6, 6)
                    ),
                    'crs' => array(
                        'type' => 'name',
                        'properties' => array(
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                        )
                    )
                ),
                array(
                    'type' => 'LineString',
                    'coordinates' => array(
                        array(7, 7),
                        array(8, 8)
                    ),
                    'crs' => array(
                        'type' => 'name',
                        'properties' => array(
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                        )
                    )
                )
            )
        ), 4326);

        $expected = array(
            'type' => 'MultiPolygon',
            'coordinates' => array(
                array(
                    array(array(1, 1), array(2, 2)),
                    array(array(3, 3), array(4, 4))
                ),
                array(
                    array(array(5, 5), array(6, 6)),
                    array(array(7, 7), array(8, 8))
                )
            ),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateGeometryCollection()
    {
        $factory = new Factory();

        $geometry = $factory->createGeometryCollection(Dimension::DIMENSION_4D, array(
            array(
                'type' => 'Polygon',
                'coordinates' =>  array(
                    array(array(1, 1), array(2, 2)),
                    array(array(3, 3), array(4, 4))
                ),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            ),
            array(
                'type' => 'LineString',
                'coordinates' =>  array(array(1, 1), array(2, 2)),
                'crs' => array(
                    'type' => 'name',
                    'properties' => array(
                        'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                    )
                )
            ),
        ), 4326);

        $expected = array(
            'type' => 'GeometryCollection',
            'geometries' => array(
                array(
                    'type' => 'Polygon',
                    'coordinates' =>  array(
                        array(array(1, 1), array(2, 2)),
                        array(array(3, 3), array(4, 4))
                    ),
                    'crs' => array(
                        'type' => 'name',
                        'properties' => array(
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                        )
                    )
                ),
                array(
                    'type' => 'LineString',
                    'coordinates' =>  array(array(1, 1), array(2, 2)),
                    'crs' => array(
                        'type' => 'name',
                        'properties' => array(
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                        )
                    )
                ),
            ),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $geometry);
    }
}
