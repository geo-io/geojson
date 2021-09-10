<?php

declare(strict_types=1);

namespace GeoIO\GeoJSON;

use GeoIO\Coordinates;
use GeoIO\Dimension;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testCreatePoint(): void
    {
        $factory = new Factory();

        $geometry = $factory->createPoint(
            Dimension::DIMENSION_4D,
            4326,
            new Coordinates(
                x: 1,
                y: 2,
                z: 3,
                m: 4,
            ),
        );

        $expected = [
            'type' => 'Point',
            'coordinates' => [1, 2, 3, 4],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateEmptyPoint(): void
    {
        $factory = new Factory();

        $geometry = $factory->createPoint(
            Dimension::DIMENSION_4D,
            4326,
            null,
        );

        $expected = [
            'type' => 'Point',
            'coordinates' => [],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreatePointM(): void
    {
        $factory = new Factory();

        $geometry = $factory->createPoint(
            Dimension::DIMENSION_4D,
            4326,
            new Coordinates(
                x: 1,
                y: 2,
                z: null,
                m: 4,
            ),
        );

        $expected = [
            'type' => 'Point',
            'coordinates' => [1, 2, null, 4],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateLineString(): void
    {
        $factory = new Factory();

        $geometry = $factory->createLineString(
            Dimension::DIMENSION_4D,
            4326,
            [
                [
                    'type' => 'Point',
                    'coordinates' => [1, 1],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
                [
                    'type' => 'Point',
                    'coordinates' => [2, 2],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
            ],
        );

        $expected = [
            'type' => 'LineString',
            'coordinates' => [
                [1, 1],
                [2, 2],
            ],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateLinearRing(): void
    {
        $factory = new Factory();

        $geometry = $factory->createLinearRing(
            Dimension::DIMENSION_4D,
            4326,
            [
                [
                    'type' => 'Point',
                    'coordinates' => [1, 1],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
                [
                    'type' => 'Point',
                    'coordinates' => [2, 2],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
            ],
        );

        $expected = [
            'type' => 'LineString',
            'coordinates' => [
                [1, 1],
                [2, 2],
            ],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreatePolygon(): void
    {
        $factory = new Factory();

        $geometry = $factory->createPolygon(
            Dimension::DIMENSION_4D,
            4326,
            [
                [
                    'type' => 'LineString',
                    'coordinates' => [
                        [1, 1],
                        [2, 2],
                    ],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
                [
                    'type' => 'LineString',
                    'coordinates' => [
                        [3, 3],
                        [4, 4],
                    ],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
            ],
        );

        $expected = [
            'type' => 'Polygon',
            'coordinates' => [
                [[1, 1], [2, 2]],
                [[3, 3], [4, 4]],
            ],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateMultiPoint(): void
    {
        $factory = new Factory();

        $geometry = $factory->createMultiPoint(
            Dimension::DIMENSION_4D,
            4326,
            [
                [
                    'type' => 'Point',
                    'coordinates' => [1, 1],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
                [
                    'type' => 'Point',
                    'coordinates' => [2, 2],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
            ],
        );

        $expected = [
            'type' => 'MultiPoint',
            'coordinates' => [
                [1, 1],
                [2, 2],
            ],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateMultiLineString(): void
    {
        $factory = new Factory();

        $geometry = $factory->createMultiLineString(
            Dimension::DIMENSION_4D,
            4326,
            [
                [
                    'type' => 'LineString',
                    'coordinates' => [
                        [1, 1],
                        [2, 2],
                    ],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
                [
                    'type' => 'LineString',
                    'coordinates' => [
                        [3, 3],
                        [4, 4],
                    ],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
            ],
        );

        $expected = [
            'type' => 'MultiLineString',
            'coordinates' => [
                [[1, 1], [2, 2]],
                [[3, 3], [4, 4]],
            ],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateMultiPolygon(): void
    {
        $factory = new Factory();

        $geometry = $factory->createMultiPolygon(
            Dimension::DIMENSION_4D,
            4326,
            [
                [
                    [
                        'type' => 'LineString',
                        'coordinates' => [
                            [1, 1],
                            [2, 2],
                        ],
                        'crs' => [
                            'type' => 'name',
                            'properties' => [
                                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                            ],
                        ],
                    ],
                    [
                        'type' => 'LineString',
                        'coordinates' => [
                            [3, 3],
                            [4, 4],
                        ],
                        'crs' => [
                            'type' => 'name',
                            'properties' => [
                                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                            ],
                        ],
                    ],
                ],
                [
                    [
                        'type' => 'LineString',
                        'coordinates' => [
                            [5, 5],
                            [6, 6],
                        ],
                        'crs' => [
                            'type' => 'name',
                            'properties' => [
                                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                            ],
                        ],
                    ],
                    [
                        'type' => 'LineString',
                        'coordinates' => [
                            [7, 7],
                            [8, 8],
                        ],
                        'crs' => [
                            'type' => 'name',
                            'properties' => [
                                'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                            ],
                        ],
                    ],
                ],
            ],
        );

        $expected = [
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
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }

    public function testCreateGeometryCollection(): void
    {
        $factory = new Factory();

        $geometry = $factory->createGeometryCollection(
            Dimension::DIMENSION_4D,
            4326,
            [
                [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [[1, 1], [2, 2]],
                        [[3, 3], [4, 4]],
                    ],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
                [
                    'type' => 'LineString',
                    'coordinates' => [[1, 1], [2, 2]],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
            ],
        );

        $expected = [
            'type' => 'GeometryCollection',
            'geometries' => [
                [
                    'type' => 'Polygon',
                    'coordinates' => [
                        [[1, 1], [2, 2]],
                        [[3, 3], [4, 4]],
                    ],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
                [
                    'type' => 'LineString',
                    'coordinates' => [[1, 1], [2, 2]],
                    'crs' => [
                        'type' => 'name',
                        'properties' => [
                            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                        ],
                    ],
                ],
            ],
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84',
                ],
            ],
        ];

        $this->assertEquals($expected, $geometry);
    }
}
