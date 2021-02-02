<?php

namespace Jordy\Http\Tests;

use Jordy\Http\Api\AbstractEndpoint;
use Jordy\Http\Client;
use Jordy\Http\Network\CurlTransport;
use Jordy\Http\Network\TransportOutput;
use Jordy\Http\ResponseList;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testTransfer()
    {
        $this->markTestIncomplete("Should cover more test cases");

        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn(new TransportOutput());

        $client = (new Client())
            ->setTransporter($stubTransport)
            ->setHeaders([
                "Content-Type" => "application/json"
            ]);

        $response = $client->transfer(
            "GET",
            "https://www.google.nl/",
            [],
            null,
            new ResponseList()
        );

        $this->assertTrue($response instanceof ResponseList);
        $this->assertEquals("GET", $response->getRequest()->getMethod());
        $this->assertEquals("https://www.google.nl/", $response->getRequest()->getUri());
    }

    public function testTransferEndpoint()
    {
        $this->markTestIncomplete("Should cover more test cases");

        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn(new TransportOutput());

        $stubEndpoint = $this->createMock(AbstractEndpoint::class);
        $stubEndpoint->method("getPrototype")
            ->willReturn(new ResponseList());

        $stubEndpoint->method("getUri")
            ->willReturn("https://www.google.nl/");

        $stubEndpoint->method("getQueryParams")
            ->willReturn([
                "foo" => "bar",
                "tags" => [
                    "one",
                    "two",
                    "three"
                ]
            ]);
        $stubEndpoint->method("getRequestBody")
            ->willReturn([
                "test" => "ok"
            ]);

        $client = (new Client())
            ->setTransporter($stubTransport);

        $response = $client->transferEndpoint("GET", $stubEndpoint);

        $this->assertTrue($response instanceof ResponseList);
        $this->assertEquals("GET", $response->getRequest()->getMethod());
        $this->assertEquals(
            "https://www.google.nl/?foo=bar&tags%5B0%5D=one&tags%5B1%5D=two&tags%5B2%5D=three",
            $response->getRequest()->getQueriedUri()
        );
    }
}
