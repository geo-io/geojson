<?php

declare(strict_types=1);

namespace GeoIO\GeoJSON;

use GeoIO\Dimension;
use GeoIO\GeoJSON\Exception\InvalidGeometryException;
use GeoIO\GeometryType;
use JsonSerializable;
use PHPUnit\Framework\TestCase;
use stdClass;

class ExtractorTest extends TestCase
{
    public function testSupports(): void
    {
        $extractor = new Extractor();

        $this->assertTrue($extractor->supports([
            'type' => 'Point',
        ]));
        $this->assertTrue($extractor->supports([
            'type' => 'LineString',
        ]));
        $this->assertTrue($extractor->supports([
            'type' => 'Polygon',
        ]));
        $this->assertTrue($extractor->supports([
            'type' => 'MultiPoint',
        ]));
        $this->assertTrue($extractor->supports([
            'type' => 'MultiLineString',
        ]));
        $this->assertTrue($extractor->supports([
            'type' => 'MultiPolygon',
        ]));
        $this->assertTrue($extractor->supports([
            'type' => 'GeometryCollection',
        ]));

        $this->assertFalse($extractor->supports(new stdClass()));
        $this->assertFalse($extractor->supports(null));
        $this->assertFalse($extractor->supports('foo'));
    }

    public function testExtractType(): void
    {
        $extractor = new Extractor();

        $this->assertEquals(GeometryType::POINT, $extractor->extractType([
            'type' => 'Point',
        ]));
        $this->assertEquals(GeometryType::LINESTRING, $extractor->extractType([
            'type' => 'LineString',
        ]));
        $this->assertEquals(GeometryType::POLYGON, $extractor->extractType([
            'type' => 'Polygon',
        ]));
        $this->assertEquals(GeometryType::MULTIPOINT, $extractor->extractType([
            'type' => 'MultiPoint',
        ]));
        $this->assertEquals(GeometryType::MULTILINESTRING, $extractor->extractType([
            'type' => 'MultiLineString',
        ]));
        $this->assertEquals(GeometryType::MULTIPOLYGON, $extractor->extractType([
            'type' => 'MultiPolygon',
        ]));
        $this->assertEquals(GeometryType::GEOMETRYCOLLECTION, $extractor->extractType([
            'type' => 'GeometryCollection',
        ]));
    }

    public function testExtractTypeThrowsExceptionForInvalidGeometry(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractType(new stdClass());
    }

    public function testExtractTypeThrowsExceptionForInvalidGeometryType(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractType([
            'type' => 'Invalid',
        ]);
    }

    /**
     * @dataProvider extractDimensionProvider
     */
    public function testExtractDimension(array $geometry, Dimension $expectedDimension): void
    {
        $extractor = new Extractor();

        $this->assertEquals(
            $expectedDimension,
            $extractor->extractDimension($geometry)
        );
    }

    public function extractDimensionProvider(): array
    {
        return [
            [
                [
                    'type' => 'Point',
                    'coordinates' => [],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'Point',
                    'coordinates' => [1, 1],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'Point',
                    'coordinates' => [1, 1, 1],
                ],
                Dimension::DIMENSION_3DZ,
            ],
            [
                [
                    'type' => 'Point',
                    'coordinates' => [1, 1, null, 1],
                ],
                Dimension::DIMENSION_3DM,
            ],
            [
                [
                    'type' => 'Point',
                    'coordinates' => [1, 1, 1, 1],
                ],
                Dimension::DIMENSION_4D,
            ],

            [
                [
                    'type' => 'LineString',
                    'coordinates' => [],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'LineString',
                    [
                        [1, 1],
                        [2, 2],
                    ],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'LineString',
                    'coordinates' => [
                        [1, 1, 1],
                        [2, 2, 2],
                    ],
                ],
                Dimension::DIMENSION_3DZ,
            ],
            [
                [
                    'type' => 'LineString',
                    'coordinates' => [
                        [1, 1, null, 1],
                        [2, 2, null, 2],
                    ],
                ],
                Dimension::DIMENSION_3DM,
            ],
            [
                [
                    'type' => 'LineString',
                    'coordinates' => [
                        [1, 1, 1, 1],
                        [2, 2, 2, 2],
                    ],
                ],
                Dimension::DIMENSION_4D,
            ],

            [
                [
                    'type' => 'Polygon',
                    'coordinates' => [],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [[1, 1]],
                        [[2, 2]],
                    ],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [[1, 1, 1]],
                        [[2, 2, 2]],
                    ],
                ],
                Dimension::DIMENSION_3DZ,
            ],
            [
                [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [[1, 1, null, 1]],
                        [[2, 2, null, 2]],
                    ],
                ],
                Dimension::DIMENSION_3DM,
            ],
            [
                [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [[1, 1, 1, 1]],
                        [[2, 2, 2, 2]],
                    ],
                ],
                Dimension::DIMENSION_4D,
            ],

            [
                [
                    'type' => 'MultiPoint',
                    'coordinates' => [],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'MultiPoint',
                    'coordinates' => [
                        [1, 1],
                        [2, 2],
                    ],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'MultiPoint',
                    'coordinates' => [
                        [1, 1, 1],
                        [2, 2, 2],
                    ],
                ],
                Dimension::DIMENSION_3DZ,
            ],
            [
                [
                    'type' => 'MultiPoint',
                    'coordinates' => [
                        [1, 1, null, 1],
                        [2, 2, null, 2],
                    ],
                ],
                Dimension::DIMENSION_3DM,
            ],
            [
                [
                    'type' => 'MultiPoint',
                    'coordinates' => [
                        [1, 1, 1, 1],
                        [2, 2, 2, 2],
                    ],
                ],
                Dimension::DIMENSION_4D,
            ],

            [
                [
                    'type' => 'MultiLineString',
                    'coordinates' => [],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'MultiLineString',
                    'coordinates' => [
                        [[1, 1]],
                        [[2, 2]],
                    ],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'MultiLineString',
                    'coordinates' => [
                        [[1, 1, 1]],
                        [[2, 2, 2]],
                    ],
                ],
                Dimension::DIMENSION_3DZ,
            ],
            [
                [
                    'type' => 'MultiLineString',
                    'coordinates' => [
                        [[1, 1, null, 1]],
                        [[2, 2, null, 2]],
                    ],
                ],
                Dimension::DIMENSION_3DM,
            ],
            [
                [
                    'type' => 'MultiLineString',
                    'coordinates' => [
                        [[1, 1, 1, 1]],
                        [[2, 2, 2, 2]],
                    ],
                ],
                Dimension::DIMENSION_4D,
            ],

            [
                [
                    'type' => 'MultiPolygon',
                    'coordinates' => [],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'MultiPolygon',
                    'coordinates' => [
                        [
                            [[1, 1]],
                            [[2, 2]],
                        ],
                    ],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'MultiPolygon',
                    'coordinates' => [
                        [
                            [[1, 1, 1]],
                            [[2, 2, 2]],
                        ],
                    ],
                ],
                Dimension::DIMENSION_3DZ,
            ],
            [
                [
                    'type' => 'MultiPolygon',
                    'coordinates' => [
                        [
                            [[1, 1, null, 1]],
                            [[2, 2, null, 2]],
                        ],
                    ],
                ],
                Dimension::DIMENSION_3DM,
            ],
            [
                [
                    'type' => 'MultiPolygon',
                    'coordinates' => [
                        [
                            [[1, 1, 1, 1]],
                            [[2, 2, 2, 2]],
                        ],
                    ],
                ],
                Dimension::DIMENSION_4D,
            ],

            [
                [
                    'type' => 'GeometryCollection',
                    'geometries' => [],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'GeometryCollection',
                    'geometries' => [
                        [
                            'type' => 'Point',
                            'coordinates' => [
                                1, 1,
                            ],
                        ],
                    ],
                ],
                Dimension::DIMENSION_2D,
            ],
            [
                [
                    'type' => 'GeometryCollection',
                    'geometries' => [
                        [
                            'type' => 'Point',
                            'coordinates' => [
                                1, 1, 1,
                            ],
                        ],
                    ],
                ],
                Dimension::DIMENSION_3DZ,
            ],
            [
                [
                    'type' => 'GeometryCollection',
                    'geometries' => [
                        [
                            'type' => 'Point',
                            'coordinates' => [
                                1, 1, null, 1,
                            ],
                        ],
                    ],
                ],
                Dimension::DIMENSION_3DM,
            ],
            [
                [
                    'type' => 'GeometryCollection',
                    'geometries' => [
                        [
                            'type' => 'Point',
                            'coordinates' => [
                                1, 1, 1, 1,
                            ],
                        ],
                    ],
                ],
                Dimension::DIMENSION_4D,
            ],
        ];
    }

    public function testExtractDimensionThrowsExceptionForInvalidGeometry(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractDimension(new stdClass());
    }

    public function testExtractDimensionThrowsExceptionForInvalidGeometryType(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractDimension([
            'type' => 'Invalid',
        ]);
    }

    public function testExtractSrid(): void
    {
        $extractor = new Extractor();

        $json = [
            'type' => 'Point',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals(4326, $extractor->extractSrid($json));

        $json = [
            'type' => 'Point',
            'crs' => [
                'type' => 'link',
                'properties' => [
                    'href' => 'http://spatialreference.org/ref/epsg/4326/',
                ],
            ],
        ];

        $this->assertEquals(4326, $extractor->extractSrid($json));
    }

    public function testExtractSridReturnsNullForMissingSrid(): void
    {
        $extractor = new Extractor();

        $json = [
            'type' => 'Point',
        ];

        $this->assertNull($extractor->extractSrid($json));
    }

    public function testExtractSridThrowsExceptionForInvalidGeometryType(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractSrid([
            'type' => 'Invalid',
        ]);
    }

    public function testExtractCoordinatesFromPoint(): void
    {
        $json = [
            'type' => 'Point',
            'coordinates' => [1, 2, 3, 4],
        ];

        $extractor = new Extractor();

        $coordinates = $extractor->extractCoordinatesFromPoint($json);

        $this->assertEquals(1, $coordinates->x);
        $this->assertEquals(2, $coordinates->y);
        $this->assertEquals(3, $coordinates->z);
        $this->assertEquals(4, $coordinates->m);
    }

    public function testExtractCoordinatesFromPointFeature(): void
    {
        $json = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [1, 2, 3, 4],
            ],
        ];

        $extractor = new Extractor();

        $coordinates = $extractor->extractCoordinatesFromPoint($json);

        $this->assertEquals(1, $coordinates->x);
        $this->assertEquals(2, $coordinates->y);
        $this->assertEquals(3, $coordinates->z);
        $this->assertEquals(4, $coordinates->m);
    }

    public function testExtractCoordinatesFromEmptyPoint(): void
    {
        $json = [
            'type' => 'Point',
            'coordinates' => [],
        ];

        $extractor = new Extractor();

        $coordinates = $extractor->extractCoordinatesFromPoint($json);

        $this->assertNull($coordinates);
    }

    public function testExtractCoordinatesFromPointThrowsExceptionForUnexpectedGeometryType(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractCoordinatesFromPoint([
            'type' => 'LineString',
            'coordinates' => [],
        ]);
    }

    public function testExtractCoordinatesFromPointThrowsExceptionForInvalidCoordinates(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractCoordinatesFromPoint([
            'type' => 'Point',
            'coordinates' => null,
        ]);
    }

    public function testExtractPointsFromLineString(): void
    {
        $json = [
            'type' => 'LineString',
            'coordinates' => [
                [1, 1],
                [2, 2],
            ],
        ];

        $expected = [
            [
                'type' => 'Point',
                'coordinates' => [1, 1],
            ],
            [
                'type' => 'Point',
                'coordinates' => [2, 2],
            ],
        ];

        $extractor = new Extractor();

        $array = $extractor->extractPointsFromLineString($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractPointsFromLineStringThrowsExceptionForInvalidCoordinates(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractPointsFromLineString([
            'type' => 'LineString',
            'coordinates' => null,
        ]);
    }

    public function testExtractLineStringsFromPolygon(): void
    {
        $json = [
            'type' => 'Polygon',
            'coordinates' => [
                [[1, 1], [2, 2]],
                [[3, 3], [4, 4]],
            ],
        ];

        $expected = [
            [
                'type' => 'LineString',
                'coordinates' => [[1, 1], [2, 2]],
            ],
            [
                'type' => 'LineString',
                'coordinates' => [[3, 3], [4, 4]],
            ],
        ];

        $extractor = new Extractor();

        $array = $extractor->extractLineStringsFromPolygon($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractLineStringsFromPolygonThrowsExceptionForInvalidCoordinates(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractLineStringsFromPolygon([
            'type' => 'Polygon',
            'coordinates' => null,
        ]);
    }

    public function testExtractPointsFromMultiPoint(): void
    {
        $json = [
            'type' => 'MultiPoint',
            'coordinates' => [
                [1, 1], [2, 2],
            ],
        ];

        $expected = [
            [
                'type' => 'Point',
                'coordinates' => [1, 1],
            ],
            [
                'type' => 'Point',
                'coordinates' => [2, 2],
            ],
        ];

        $extractor = new Extractor();

        $array = $extractor->extractPointsFromMultiPoint($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractPointsFromMultiPointThrowsExceptionForInvalidCoordinates(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractPointsFromMultiPoint([
            'type' => 'MultiPoint',
            'coordinates' => null,
        ]);
    }

    public function testExtractLineStringsFromMultiLineString(): void
    {
        $json = [
            'type' => 'MultiLineString',
            'coordinates' => [
                [[1, 1], [2, 2]],
                [[3, 3], [4, 5]],
            ],
        ];

        $expected = [
            [
                'type' => 'LineString',
                'coordinates' => [[1, 1], [2, 2]],
            ],
            [
                'type' => 'LineString',
                'coordinates' => [[3, 3], [4, 5]],
            ],
        ];

        $extractor = new Extractor();

        $array = $extractor->extractLineStringsFromMultiLineString($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractLineStringsFromMultiLineStringThrowsExceptionForInvalidCoordinates(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractLineStringsFromMultiLineString([
            'type' => 'MultiLineString',
            'coordinates' => null,
        ]);
    }

    public function testExtractMultiPolygon(): void
    {
        $json = [
            'type' => 'MultiPolygon',
            'coordinates' => [
                [
                    [[1, 1], [2, 2]],
                    [[3, 3], [4, 4]],
                ],
                [
                    [[5, 5], [6, 6]],
                    [[7, 7], [8, 8]],
                ],
            ],
        ];

        $expected = [
            [
                'type' => 'Polygon',
                'coordinates' => [
                    [[1, 1], [2, 2]],
                    [[3, 3], [4, 4]],
                ],
            ],
            [
                'type' => 'Polygon',
                'coordinates' => [
                    [[5, 5], [6, 6]],
                    [[7, 7], [8, 8]],
                ],
            ],
        ];

        $extractor = new Extractor();

        $array = $extractor->extractPolygonsFromMultiPolygon($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractMultiPolygonThrowsExceptionForInvalidCoordinates(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractPolygonsFromMultiPolygon([
            'type' => 'MultiPolygon',
            'coordinates' => null,
        ]);
    }

    public function testExtractGeometryCollection(): void
    {
        $json = [
            'type' => 'GeometryCollection',
            'geometries' => [
                [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [[1, 1], [2, 2]],
                        [[3, 3], [4, 4]],
                    ],
                ],
                [
                    'type' => 'LineString',
                    'coordinates' => [[1, 1], [2, 2]],
                ],
            ],
        ];

        $expected = [
            [
                'type' => 'Polygon',
                'coordinates' => [
                    [[1, 1], [2, 2]],
                    [[3, 3], [4, 4]],
                ],
            ],
            [
                'type' => 'LineString',
                'coordinates' => [[1, 1], [2, 2]],
            ],
        ];

        $extractor = new Extractor();

        $array = $extractor->extractGeometriesFromGeometryCollection($json);

        $this->assertEquals($expected, $array);
    }

    public function testExtractGeometryCollectionThrowsExceptionForInvalidCoordinates(): void
    {
        $this->expectException(InvalidGeometryException::class);

        $extractor = new Extractor();
        $extractor->extractGeometriesFromGeometryCollection([
            'type' => 'GeometryCollection',
            'geometries' => null,
        ]);
    }

    public function testTryConvertToArrayConvertsString(): void
    {
        $extractor = new Extractor();
        $array = $extractor->convertToArray(
            json_encode(
                [
                    'type' => 'Point',
                    'coordinates' => [
                        1, 1, 1, 1,
                    ],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ]
            )
        );

        $expected = [
            'type' => 'Point',
            'coordinates' => [1, 1, 1, 1],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $array);
    }

    public function testTryConvertToArrayConvertsObject(): void
    {
        $extractor = new Extractor();
        $array = $extractor->convertToArray(
            json_decode(
                json_encode(
                    [
                        'type' => 'Point',
                        'coordinates' => [
                            1, 1, 1, 1,
                        ],
                        'crs' => [
                            'type' => 'name',
                            'properties' => [
                                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                            ],
                        ],
                    ]
                )
            )
        );

        $expected = [
            'type' => 'Point',
            'coordinates' => [1, 1, 1, 1],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $array);
    }

    public function testTryConvertToArrayConvertsJsonSerializable(): void
    {
        $extractor = new Extractor();
        $array = $extractor->convertToArray(
            new class() implements JsonSerializable {
                public function jsonSerialize(): array
                {
                    return [
                        'type' => 'Point',
                        'coordinates' => [
                            1, 1, 1, 1,
                        ],
                        'crs' => [
                            'type' => 'name',
                            'properties' => [
                                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                            ],
                        ],
                    ];
                }
            }
        );

        $expected = [
            'type' => 'Point',
            'coordinates' => [1, 1, 1, 1],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $array);
    }

    public function testTryConvertToArrayConvertsInvalidJson(): void
    {
        $extractor = new Extractor();
        $array = $extractor->convertToArray('{');

        $this->assertEquals('{', $array);
    }
}
