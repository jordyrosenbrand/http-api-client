<?php

namespace Jordy\Http\Tests;

use Jordy\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @return array[]
     */
    public function statusCodeProvider()
    {
        return [
            [200, true],
            [201, true],
            [400, false],
            [500, false]
        ];
    }

    /**
     * @dataProvider statusCodeProvider
     */
    public function testIsValid($statusCode, $isValid)
    {
        $response = new Response();
        $response->setStatusCode($statusCode);

        $this->assertEquals($isValid, $response->isValid());
    }

    /**
     * @return \string[][]
     */
    public function responseHeaderProvider()
    {
        return [
            ["Content-Type", "application/json"]
        ];
    }

    /**
     * @dataProvider responseHeaderProvider
     */
    public function testResponseHeader($header, $headerValue)
    {
        $response = new Response();
        $response->setResponseHeaders([
            $header => $headerValue,
        ]);

        $this->assertEquals($headerValue, $response->getResponseHeader($header));
    }

    /**
     * @return array[]
     */
    public function responseBodyProvider()
    {
        return [
            [["id" => 1], "id", 1],
            [["address" => ["street" => "Hoofdstraat"]], ["address", "street"], "Hoofdstraat"],
            ["title" => "Not important", "description", null],
            ["title" => "Not Important", "Title", null],
            [json_decode(json_encode(["results" => "data"])), "results", "data"], // stdclass
        ];
    }

    /**
     * @dataProvider responseBodyProvider
     */
    public function testExtractFromBody($responseBody, $path, $expected)
    {
        $response = new Response();
        $response->setResponseBody($responseBody);

        $this->assertEquals($expected, $response->extractFromBody($path));
    }
}
