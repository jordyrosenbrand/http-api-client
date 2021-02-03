<?php

namespace Jordy\Http\Tests;

use Jordy\Http\Request;
use Jordy\Http\Response;
use Jordy\Http\ResponseList;
use PHPUnit\Framework\TestCase;

class ResponseListTest extends TestCase
{
    /**
     *
     */
    public function testGetPrototypeIsClone()
    {
        $response = new Response();
        $list = new ResponseList($response);

        $this->assertTrue($response !== $list->getPrototype());
    }

    /**
     * @return array
     */
    public function responseBodyProvider()
    {
        return [
            [
                [["id" => 1], ["id" => 2]],
                [],
                [["id" => 1], ["id" => 2]]
            ],
            [
                [["id" => 1], ["id" => 2]],
                "",
                [["id" => 1], ["id" => 2]]
            ],
            [
                ["results" => ["id" => 1]],
                ["results"],
                [["id" => 1]]
            ],
            [
                ["results" => ["data" => [["id" => 1]]]],
                ["results", "data"],
                [["id" => 1]]
            ],
            [
                ["success"],
                "",
                [["success"]]
            ],
            [
                "success",
                "",
                [["success"]]
            ],
            [
                [],
                [],
                []
            ]
        ];
    }

    /**
     * @dataProvider responseBodyProvider
     */
    public function testItems($responseBody, $resultMapping, $items)
    {
        $list = (new ResponseList())
            ->setResponseBody($responseBody)
            ->setResultMapping($resultMapping);

        $this->assertEquals($items, $list->getItems());
    }

    /**
     * @dataProvider responseBodyProvider
     */
    public function testCount($responseBody, $resultMapping, $items)
    {
        $list = (new ResponseList())
            ->setResponseBody($responseBody)
            ->setResultMapping($resultMapping);

        $this->assertEquals(count($items), $list->count());
    }

    /**
     * @return array[]
     */
    public function columnProvider()
    {
        return [
            [
                [["id" => 1], ["id" => 2], ["id" => 3]],
                "id",
                [1, 2, 3]
            ],
            [
                [],
                "id",
                []
            ]
        ];
    }

    /**
     * @dataProvider columnProvider
     */
    public function testColumn($responseBody, $column, $expected)
    {
        $list = (new ResponseList())
            ->setResponseBody($responseBody);

        $this->assertEquals($expected, $list->column($column));
    }

    /**
     * @return array[]
     */
    public function mapProvider()
    {
        return [
            [
                [["id" => 1, "title" => "First"], ["id" => 2, "title" => "Second"]],
                "id",
                "title",
                [1 => "First", 2 => "Second"]
            ],
            [
                [],
                "id",
                "title",
                []
            ]
        ];
    }

    /**
     * @dataProvider mapProvider
     */
    public function testMap($responseBody, $keyColumn, $valueColumn, $expected)
    {
        $list = (new ResponseList())
            ->setResponseBody($responseBody);

        $this->assertEquals($expected, $list->map($keyColumn, $valueColumn));
    }

    /**
     * @return array[]
     */
    public function firstProvider()
    {
        return [
            [
                [["id" => 1], ["id" => 2]],
                [],
                ["id" => 1]
            ],
            [
                ["result" => []],
                ["result"],
                []
            ]
        ];
    }

    /**
     * @dataProvider firstProvider
     */
    public function testFirst($responseBody, $resultMapping, $expectedResponseBody)
    {
        $list = (new ResponseList(new Response()))
            ->setRequest(new Request())
            ->setResponseBody($responseBody)
            ->setResultMapping($resultMapping);

        $response = (new Response())
            ->setRequest(new Request())
            ->setResponseBody($expectedResponseBody);

        $this->assertEquals($response, $list->first());
    }

    /**
     * @return array[]
     */
    public function lastProvider()
    {
        return [
            [
                [["id" => 1], ["id" => 2]],
                [],
                ["id" => 2]
            ],
            [
                ["result" => []],
                ["result"],
                []
            ]
        ];
    }

    /**
     * @dataProvider lastProvider
     */
    public function testLast($responseBody, $resultMapping, $expectedResponseBody)
    {
        $list = (new ResponseList(new Response()))
            ->setRequest(new Request())
            ->setResponseBody($responseBody)
            ->setResultMapping($resultMapping);

        $response = (new Response())
            ->setRequest(new Request())
            ->setResponseBody($expectedResponseBody);

        $this->assertEquals($response, $list->last());
    }

    /**
     * @return array[]
     */
    public function findProvider()
    {
        return [
            [
                [["id" => 1], ["id" => 2]],
                "id",
                1,
                ["id" => 1]
            ],
            [
                [["id" => 1]],
                "id",
                2,
                null
            ]
        ];
    }

    /**
     * @dataProvider findProvider
     */
    public function testFind($responseBody, $column, $value, $expectedResponseBody)
    {
        $list = (new ResponseList(new Response()))
            ->setRequest(new Request())
            ->setResponseBody($responseBody);

        $response = (new Response())
            ->setRequest(new Request())
            ->setResponseBody($expectedResponseBody);

        $expect = $expectedResponseBody ? $response : $expectedResponseBody;

        $this->assertEquals($expect, $list->find($column, $value));
    }

    /**
     *
     */
    public function testToNestedArray()
    {
        $responseBody = [
            ["id" => 1],
            ["id" => 2]
        ];

        $list = (new ResponseList())
            ->setRequest(new Request())
            ->setResponseBody($responseBody);

        $this->assertEquals($responseBody, $list->toNestedArray());
    }

    /**
     *
     */
    public function testGetIterator()
    {
        $list = (new ResponseList(new Response()))
            ->setRequest(new Request())
            ->setResponseBody([
                "results" => [
                    ["id" => 1],
                    ["id" => 2]
                ]
            ])
            ->setResultMapping("results");

        foreach($list as $item) {
            $this->assertTrue($item instanceof Response);
        }
    }
}
