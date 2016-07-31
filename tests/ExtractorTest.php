<?php

namespace GeoIO\GeoJSON;

use GeoIO\Dimension;
use GeoIO\Extractor as ExtractorInterface;

class ExtractorTest  extends \PHPUnit_Framework_TestCase
{
    public function testSupports()
    {
        $extractor = new Extractor();

        $this->assertTrue($extractor->supports(array(
            'type' => 'Point'
        )));
        $this->assertTrue($extractor->supports(array(
            'type' => 'LineString'
        )));
        $this->assertTrue($extractor->supports(array(
            'type' => 'Polygon'
        )));
        $this->assertTrue($extractor->supports(array(
            'type' => 'MultiPoint'
        )));
        $this->assertTrue($extractor->supports(array(
            'type' => 'MultiLineString'
        )));
        $this->assertTrue($extractor->supports(array(
            'type' => 'MultiPolygon'
        )));
        $this->assertTrue($extractor->supports(array(
            'type' => 'GeometryCollection'
        )));

        $this->assertFalse($extractor->supports(new \stdClass()));
        $this->assertFalse($extractor->supports(null));
        $this->assertFalse($extractor->supports('foo'));
    }

    public function testExtractType()
    {
        $extractor = new Extractor();

        $this->assertSame(ExtractorInterface::TYPE_POINT, $extractor->extractType(array(
            'type' => 'Point'
        )));
        $this->assertSame(ExtractorInterface::TYPE_LINESTRING, $extractor->extractType(array(
            'type' => 'LineString'
        )));
        $this->assertSame(ExtractorInterface::TYPE_POLYGON, $extractor->extractType(array(
            'type' => 'Polygon'
        )));
        $this->assertSame(ExtractorInterface::TYPE_MULTIPOINT, $extractor->extractType(array(
            'type' => 'MultiPoint'
        )));
        $this->assertSame(ExtractorInterface::TYPE_MULTILINESTRING, $extractor->extractType(array(
            'type' => 'MultiLineString'
        )));
        $this->assertSame(ExtractorInterface::TYPE_MULTIPOLYGON, $extractor->extractType(array(
            'type' => 'MultiPolygon'
        )));
        $this->assertSame(ExtractorInterface::TYPE_GEOMETRYCOLLECTION, $extractor->extractType(array(
            'type' => 'GeometryCollection'
        )));
    }

    public function testExtractTypeThrowsExceptionForInvalidGeometry()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractType(new \stdClass());
    }

    public function testExtractTypeThrowsExceptionForInvalidGeometryType()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractType(array(
            'type' => 'Invalid'
        ));
    }

    /**
     * @dataProvider testExtractDimensionProvider
     */
    public function testExtractDimension($geometry, $expectedDimension)
    {
        $extractor = new Extractor();

        $this->assertSame(
            $expectedDimension,
            $extractor->extractDimension($geometry)
        );
    }

    public function testExtractDimensionProvider()
    {
        return array(
            array(
                array(
                    'type' => 'Point',
                    'coordinates' => array()
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'Point',
                    'coordinates' => array(1, 1)
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'Point',
                    'coordinates' => array(1, 1, 1)
                ),
                Dimension::DIMENSION_3DZ
            ),
            array(
                array(
                    'type' => 'Point',
                    'coordinates' => array(1, 1, null, 1)
                ),
                Dimension::DIMENSION_3DM
            ),
            array(
                array(
                    'type' => 'Point',
                    'coordinates' => array(1, 1, 1, 1)
                ),
                Dimension::DIMENSION_4D
            ),

            array(
                array(
                    'type' => 'LineString',
                    'coordinates' => array()
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'LineString',
                    array(
                        array(1, 1),
                        array(2, 2)
                    )
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'LineString',
                    'coordinates' => array(
                        array(1, 1, 1),
                        array(2, 2, 2)
                    )
                ),
                Dimension::DIMENSION_3DZ
            ),
            array(
                array(
                    'type' => 'LineString',
                    'coordinates' => array(
                        array(1, 1, null, 1),
                        array(2, 2, null, 2)
                    )
                ),
                Dimension::DIMENSION_3DM
            ),
            array(
                array(
                    'type' => 'LineString',
                    'coordinates' => array(
                        array(1, 1, 1, 1),
                        array(2, 2, 2, 2)
                    )
                ),
                Dimension::DIMENSION_4D
            ),

            array(
                array(
                    'type' => 'Polygon',
                    'coordinates' => array()
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'Polygon',
                    'coordinates' => array(
                        array(array(1, 1)),
                        array(array(2, 2))
                    )
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'Polygon',
                    'coordinates' => array(
                        array(array(1, 1, 1)),
                        array(array(2, 2, 2))
                    )
                ),
                Dimension::DIMENSION_3DZ
            ),
            array(
                array(
                    'type' => 'Polygon',
                    'coordinates' => array(
                        array(array(1, 1, null, 1)),
                        array(array(2, 2, null, 2))
                    )
                ),
                Dimension::DIMENSION_3DM
            ),
            array(
                array(
                    'type' => 'Polygon',
                    'coordinates' => array(
                        array(array(1, 1, 1, 1)),
                        array(array(2, 2, 2, 2))
                    )
                ),
                Dimension::DIMENSION_4D
            ),

            array(
                array(
                    'type' => 'MultiPoint',
                    'coordinates' => array()
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'MultiPoint',
                    'coordinates' => array(
                        array(1, 1),
                        array(2, 2)
                    )
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'MultiPoint',
                    'coordinates' => array(
                        array(1, 1, 1),
                        array(2, 2, 2)
                    )
                ),
                Dimension::DIMENSION_3DZ
            ),
            array(
                array(
                    'type' => 'MultiPoint',
                    'coordinates' => array(
                        array(1, 1, null, 1),
                        array(2, 2, null, 2)
                    )
                ),
                Dimension::DIMENSION_3DM
            ),
            array(
                array(
                    'type' => 'MultiPoint',
                    'coordinates' => array(
                        array(1, 1, 1, 1),
                        array(2, 2, 2, 2)
                    )
                ),
                Dimension::DIMENSION_4D
            ),

            array(
                array(
                    'type' => 'MultiLineString',
                    'coordinates' => array()
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'MultiLineString',
                    'coordinates' => array(
                        array(array(1, 1)),
                        array(array(2, 2))
                    )
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'MultiLineString',
                    'coordinates' => array(
                        array(array(1, 1, 1)),
                        array(array(2, 2, 2))
                    )
                ),
                Dimension::DIMENSION_3DZ
            ),
            array(
                array(
                    'type' => 'MultiLineString',
                    'coordinates' => array(
                        array(array(1, 1, null, 1)),
                        array(array(2, 2, null, 2))
                    )
                ),
                Dimension::DIMENSION_3DM
            ),
            array(
                array(
                    'type' => 'MultiLineString',
                    'coordinates' => array(
                        array(array(1, 1, 1, 1)),
                        array(array(2, 2, 2, 2))
                    )
                ),
                Dimension::DIMENSION_4D
            ),

            array(
                array(
                    'type' => 'MultiPolygon',
                    'coordinates' => array()
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'MultiPolygon',
                    'coordinates' => array(
                        array(
                            array(array(1, 1)),
                            array(array(2, 2))
                        )
                    )
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'MultiPolygon',
                    'coordinates' => array(
                        array(
                            array(array(1, 1, 1)),
                            array(array(2, 2, 2))
                        )
                    )
                ),
                Dimension::DIMENSION_3DZ
            ),
            array(
                array(
                    'type' => 'MultiPolygon',
                    'coordinates' => array(
                        array(
                            array(array(1, 1, null, 1)),
                            array(array(2, 2, null, 2))
                        )
                    )
                ),
                Dimension::DIMENSION_3DM
            ),
            array(
                array(
                    'type' => 'MultiPolygon',
                    'coordinates' => array(
                        array(
                            array(array(1, 1, 1, 1)),
                            array(array(2, 2, 2, 2))
                        )
                    )
                ),
                Dimension::DIMENSION_4D
            ),

            array(
                array(
                    'type' => 'GeometryCollection',
                    'geometries' => array()
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'GeometryCollection',
                    'geometries' => array(
                        array(
                            'type' => 'Point',
                            'coordinates' => array(
                                1, 1
                            )
                        ),
                    )
                ),
                Dimension::DIMENSION_2D
            ),
            array(
                array(
                    'type' => 'GeometryCollection',
                    'geometries' => array(
                        array(
                            'type' => 'Point',
                            'coordinates' => array(
                                1, 1, 1
                            )
                        ),
                    )
                ),
                Dimension::DIMENSION_3DZ
            ),
            array(
                array(
                    'type' => 'GeometryCollection',
                    'geometries' => array(
                        array(
                            'type' => 'Point',
                            'coordinates' => array(
                                1, 1, null, 1
                            )
                        ),
                    )
                ),
                Dimension::DIMENSION_3DM
            ),
            array(
                array(
                    'type' => 'GeometryCollection',
                    'geometries' => array(
                        array(
                            'type' => 'Point',
                            'coordinates' => array(
                                1, 1, 1, 1
                            )
                        ),
                    )
                ),
                Dimension::DIMENSION_4D
            ),
        );
    }

    public function testExtractDimensionThrowsExceptionForInvalidGeometry()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractDimension(new \stdClass());
    }

    public function testExtractDimensionThrowsExceptionForInvalidGeometryType()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractDimension(array(
            'type' => 'Invalid'
        ));
    }

    public function testExtractSrid()
    {
        $extractor = new Extractor();

        $this->assertNull($extractor->extractSrid('foo'));
        $this->assertNull($extractor->extractSrid(array()));

        $json = array(
            'type' => 'Point',
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                )
            )
        );

        $this->assertSame(4326, $extractor->extractSrid($json));

        $json = array(
            'type' => 'Point',
            'crs' => array(
                'type' => 'link',
                'properties' => array(
                    'href' => 'http://spatialreference.org/ref/epsg/4326/',
                )
            )
        );

        $this->assertSame(4326, $extractor->extractSrid($json));
    }

    public function testExtractCoordinatesFromPoint()
    {
        $json = array(
            'type' => 'Point',
            'coordinates' => array(1, 2, 3, 4)
        );

        $extractor = new Extractor();

        $coordinates = $extractor->extractCoordinatesFromPoint($json);

        $this->assertInternalType('array', $coordinates);
        $this->assertSame(1, $coordinates['x']);
        $this->assertSame(2, $coordinates['y']);
        $this->assertSame(3, $coordinates['z']);
        $this->assertSame(4, $coordinates['m']);
    }

    public function testExtractCoordinatesFromEmptyPoint()
    {
        $json = array(
            'type' => 'Point',
            'coordinates' => array()
        );

        $extractor = new Extractor();

        $coordinates = $extractor->extractCoordinatesFromPoint($json);

        $this->assertSame(array(), $coordinates);
    }


    public function testExtractCoordinatesfromPointThrowsExceptionForInvalidCoordinates()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractCoordinatesFromPoint(array(
            'coordinates' => null
        ));
    }

    public function testExtractPointsFromLineString()
    {
        $json = array(
            'type' => 'LineString',
            'coordinates' =>  array(
                array(1, 1),
                array(2, 2)
            )
        );

        $expected = array(
            array(
                'type' => 'Point',
                'coordinates' => array(1, 1)
            ),
            array(
                'type' => 'Point',
                'coordinates' => array(2, 2)
            )
        );

        $extractor = new Extractor();

        $array = $extractor->extractPointsFromLineString($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractPointsFromLineStringThrowsExceptionForInvalidCoordinates()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractPointsFromLineString(array(
            'coordinates' => null
        ));
    }

    public function testExtractLineStringsFromPolygon()
    {
        $json = array(
            'type' => 'Polygon',
            'coordinates' => array(
                array(array(1, 1), array(2, 2)),
                array(array(3, 3), array(4, 4))
            )
        );

        $expected = array(
            array(
                'type' => 'LineString',
                'coordinates' =>  array(array(1, 1), array(2, 2))
            ),
            array(
                'type' => 'LineString',
                'coordinates' => array(array(3, 3), array(4, 4))
            )
        );

        $extractor = new Extractor();

        $array = $extractor->extractLineStringsFromPolygon($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractLineStringsFromPolygonThrowsExceptionForInvalidCoordinates()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractLineStringsFromPolygon(array(
            'coordinates' => null
        ));
    }

    public function testExtractPointsFromMultiPoint()
    {
        $json = array(
            'type' => 'MultiPoint',
            'coordinates' => array(
                array(1, 1), array(2, 2)
            )
        );

        $expected = array(
            array(
                'type' => 'Point',
                'coordinates' => array(1, 1)
            ),
            array(
                'type' => 'Point',
                'coordinates' => array(2, 2)
            )
        );

        $extractor = new Extractor();

        $array = $extractor->extractPointsFromMultiPoint($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractPointsFromMultiPointThrowsExceptionForInvalidCoordinates()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractPointsFromMultiPoint(array(
            'coordinates' => null
        ));
    }

    public function testExtractLineStringsFromMultiLineString()
    {
        $json = array(
            'type' => 'MultiLineString',
            'coordinates' => array(
                array(array(1, 1), array(2, 2)),
                array(array(3, 3), array(4, 5))
            )
        );

        $expected = array(
            array(
                'type' => 'LineString',
                'coordinates' =>  array(array(1, 1), array(2, 2))
            ),
            array(
                'type' => 'LineString',
                'coordinates' => array(array(3, 3), array(4, 5))
            )
        );

        $extractor = new Extractor();

        $array = $extractor->extractLineStringsFromMultiLineString($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractLineStringsFromMultiLineStringThrowsExceptionForInvalidCoordinates()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractLineStringsFromMultiLineString(array(
            'coordinates' => null
        ));
    }

    public function testExtractMultiPolygon()
    {
        $json = array(
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
            )
        );

        $expected = array(
            array(
                'type' => 'Polygon',
                'coordinates' =>  array(
                    array(array(1, 1), array(2, 2)),
                    array(array(3, 3), array(4, 4))
                )
            ),
            array(
                'type' => 'Polygon',
                'coordinates' => array(
                    array(array(5, 5), array(6, 6)),
                    array(array(7, 7), array(8, 8))
                )
            )
        );

        $extractor = new Extractor();

        $array = $extractor->extractPolygonsFromMultiPolygon($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractMultiPolygonThrowsExceptionForInvalidCoordinates()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractPolygonsFromMultiPolygon(array(
            'coordinates' => null
        ));
    }

    public function testExtractGeometryCollection()
    {
        $json = array(
            'type' => 'GeometryCollection',
            'geometries' => array(
                array(
                    'type' => 'Polygon',
                    'coordinates' =>  array(
                        array(array(1, 1), array(2, 2)),
                        array(array(3, 3), array(4, 4))
                    )
                ),
                array(
                    'type' => 'LineString',
                    'coordinates' =>  array(array(1, 1), array(2, 2))
                ),
            )
        );

        $expected = array(
            array(
                'type' => 'Polygon',
                'coordinates' =>  array(
                    array(array(1, 1), array(2, 2)),
                    array(array(3, 3), array(4, 4))
                )
            ),
            array(
                'type' => 'LineString',
                'coordinates' =>  array(array(1, 1), array(2, 2))
            ),
        );

        $extractor = new Extractor();

        $array = $extractor->extractGeometriesFromGeometryCollection($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractGeometryCollectionThrowsExceptionForInvalidCoordinates()
    {
        $this->setExpectedException('GeoIO\GeoJSON\Exception\InvalidGeometryException');

        $extractor = new Extractor();
        $extractor->extractGeometriesFromGeometryCollection(array(
            'geometries' => null
        ));
    }

    public function testTryConvertToArrayConvertsString()
    {
        $extractor = new Extractor();
        $array = $extractor->tryConvertToArray(
            json_encode(
                array(
                    'type' => 'Point',
                    'coordinates' => array(
                        1, 1, 1, 1
                    ),
                    'crs' => array(
                        'type' => 'name',
                        'properties' => array(
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                        )
                    )
                )
            )
        );

        $expected = array(
            'type' => 'Point',
            'coordinates' => array(1, 1, 1, 1),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $array);
    }

    public function testTryConvertToArrayConvertsObject()
    {
        $extractor = new Extractor();
        $array = $extractor->tryConvertToArray(
            json_decode(
                json_encode(
                    array(
                        'type' => 'Point',
                        'coordinates' => array(
                            1, 1, 1, 1
                        ),
                        'crs' => array(
                            'type' => 'name',
                            'properties' => array(
                                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                            )
                        )
                    )
                )
            )
        );

        $expected = array(
            'type' => 'Point',
            'coordinates' => array(1, 1, 1, 1),
            'crs' => array(
                'type' => 'name',
                'properties' => array(
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
                )
            )
        );

        $this->assertEquals($expected, $array);
    }
}
