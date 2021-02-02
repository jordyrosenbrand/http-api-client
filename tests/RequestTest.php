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

    public function testGetQueriedUri()
    {
        $request = (new Request())
            ->setUri("https://www.google.nl/")
            ->setQueryParams([
                "foo" => "bar",
                "tags" => [
                    "one",
                    "two",
                    "three"
                ]
            ]);

        $expect = "https://www.google.nl/?foo=bar&tags%5B0%5D=one&tags%5B1%5D=two&tags%5B2%5D=three";

        $this->assertEquals($expect, $request->getQueriedUri());
    }

    public function testSetHeaders()
    {
        $request = (new Request())
            ->setHeaders([
                "Content-Type" => "application/json",
                "Accept" => "application/json",
                "Token" => "fewopijfwoifwpf"
            ]);

        $expect = [
            "Content-Type" => "Content-Type: application/json",
            "Accept" => "Accept: application/json",
            "Token" => "Token: fewopijfwoifwpf"
        ];

        $this->assertEquals($expect, $request->getHeaders());
    }

    public function testGetParsedBody()
    {
        $body = ["id" => 1];
        $request = (new Request())
            ->setBody($body)
            ->setParser(new JsonParser());

        $this->assertEquals(json_encode($body), $request->getParsedBody());
    }
}
