<?php

namespace Jordy\Http\Tests;

use Jordy\Http\Parser\JsonParser;
use Jordy\Http\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /**
     *
     */
    public function testIsGet()
    {
        $request = (new Request())
            ->setMethod("GET");

        $this->assertTrue($request->isGet());
    }

    /**
     *
     */
    public function testIsPost()
    {
        $request = (new Request())
            ->setMethod("POST");

        $this->assertTrue($request->isPost());
    }

    /**
     *
     */
    public function testIsPut()
    {
        $request = (new Request())
            ->setMethod("PUT");

        $this->assertTrue($request->isPut());
    }

    /**
     *
     */
    public function testIsDelete()
    {
        $request = (new Request())
            ->setMethod("DELETE");

        $this->assertTrue($request->isDelete());
    }

    /**
     * @return array[]
     */
    public function queriedUriProvider()
    {
        return [
            [
                "https://www.google.nl/",
                [
                    "foo" => "bar",
                    "tags" => [
                        "one",
                        "two",
                        "three"
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider queriedUriProvider
     */
    public function testQueriedUrl($uri, $queryParams)
    {
        $request = (new Request())
            ->setUri($uri)
            ->setQueryParams($queryParams);

        $expect = $uri . "?" . http_build_query($queryParams);

        $this->assertEquals($expect, $request->getQueriedUri());
    }

    /**
     * @return \string[][][]
     */
    public function headerProvider()
    {
        return [
            [
                [
                    "Content-Type" => "application/json",
                    "Accept" => "application/json",
                    "Token" => "fewopijfwoifwpf"
                ]
            ]
        ];
    }

    /**
     * @dataProvider headerProvider
     */
    public function testHeaders($headers)
    {
        $request = (new Request())
            ->setHeaders($headers);

        $expect = [];
        foreach($headers as $header => $value) {
            $expect[$header] = "{$header}: {$value}";
        }

        $this->assertEquals($expect, $request->getHeaders());
    }

    /**
     * @return \int[][][]
     */
    public function bodyProvider()
    {
        return [
            [
                ["id" => 1],
            ],
        ];
    }

    /**
     * @dataProvider bodyProvider
     */
    public function testBody($body)
    {
        $request = (new Request())
            ->setBody($body)
            ->setParser(new JsonParser());

        $this->assertEquals(json_encode($body), $request->getParsedBody());
    }
}
