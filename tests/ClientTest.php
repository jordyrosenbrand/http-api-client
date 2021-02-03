<?php

namespace Jordy\Http\Tests;

use Jordy\Http\Api\AbstractEndpoint;
use Jordy\Http\Client;
use Jordy\Http\Network\CurlTransport;
use Jordy\Http\Network\TransportOutput;
use Jordy\Http\Request;
use Jordy\Http\Response;
use Jordy\Http\ResponseInterface;
use Jordy\Http\ResponseList;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     *
     */
    public function testCombineHeaders()
    {
        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn(new TransportOutput());

        $baseHeaders = [
            "Token" => "abc"
        ];
        $requestHeaders = [
            "Content-Type" => "application/json"
        ];

        $request = (new Request())
            ->setHeaders($requestHeaders);

        $response = (new Client())
            ->setTransporter($stubTransport)
            ->setHeaders($baseHeaders)
            ->transferRequest($request, new Response());

        $expectedHeaders = [];
        foreach($baseHeaders as $header => $value) {
            $expectedHeaders[$header] = "{$header}: {$value}";
        }
        foreach($requestHeaders as $header => $value) {
            $expectedHeaders[$header] = "{$header}: {$value}";
        }

        $this->assertEquals($expectedHeaders, $response->getRequest()->getHeaders());
    }

    /**
     * @return array[]
     */
    public function transferProvider()
    {
        return [
            [
                "GET",
                "https://www.google.nl/",
                ["Content-Type" => "application/json"],
                "",
                new ResponseList()
            ],
            [
                "POST",
                "https://www.google.nl/",
                ["Content-Type" => "application/json"],
                "",
                new ResponseList()
            ]
        ];
    }

    /**
     * @dataProvider transferProvider
     */
    public function testTransfer($method, $uri, $headers, $body, $response)
    {
        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn(new TransportOutput());

        $client = (new Client())
            ->setTransporter($stubTransport);

        $clientResponse = $client->transfer(
            $method,
            $uri,
            $headers,
            $body,
            $response
        );

        $expectedHeaders = [];
        foreach($headers as $header => $value) {
            $expectedHeaders[$header] = "{$header}: {$value}";
        }

        $this->assertTrue($clientResponse instanceof $response);
        $this->assertEquals($method, $clientResponse->getRequest()->getMethod());
        $this->assertEquals($uri, $clientResponse->getRequest()->getUri());
        $this->assertEquals($expectedHeaders, $clientResponse->getRequest()->getHeaders());
    }

    /**
     * @return array[]
     */
    public function endpointDataProvider()
    {
        return [
            [
                new ResponseList(),
                "https://www.google.nl/",
                [
                    "foo" => "bar",
                    "tags" => [
                        "one",
                        "two",
                        "three"
                    ]
                ],
                ["test" => "ok"]
            ],
            [
                new Response(),
                "https://www.google.nl/",
                [
                    "foo" => "bar",
                    "tags" => [
                        "one",
                        "two",
                        "three"
                    ]
                ],
                ["test" => "ok"]
            ]
        ];
    }

    /**
     * @dataProvider endpointDataProvider
     */
    public function testTransferEndpoint($prototype, $uri, $queryParams, $body)
    {
        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn(new TransportOutput());

        $client = (new Client())
            ->setTransporter($stubTransport);

        $endpoint = $this->getMockForAbstractClass(
            AbstractEndpoint::class,
            [$client]
        );

        $endpoint = $prototype instanceof ResponseList ?
            $endpoint->withResponseListPrototype($prototype) :
            $endpoint->withResponsePrototype($prototype);

        $endpoint = $endpoint->withUri($uri)
            ->withQueryParams($queryParams)
            ->withRequestBody($body);

        $response = $client->transferEndpoint("GET", $endpoint);

        $expectedUri = $uri . "?" . http_build_query($queryParams);

        $this->assertEquals($expectedUri, $response->getRequest()->getQueriedUri());
    }

    /**
     * @return array[]
     */
    public function requestDataProvider()
    {
        return [
            [
                "GET",
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
     * @dataProvider requestDataProvider
     */
    public function testTransferRequestRequest($method, $uri, $queryParams)
    {
        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn(new TransportOutput());

        $request = (new Request())
            ->setMethod($method)
            ->setUri($uri)
            ->setQueryParams($queryParams);

        $response = (new Client())
            ->setTransporter($stubTransport)
            ->transferRequest($request, new ResponseList());

        $expectedUri = $uri . "?" . http_build_query($queryParams);

        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertEquals($method, $response->getRequest()->getMethod());
        $this->assertEquals($expectedUri, $response->getRequest()->getQueriedUri());
    }

    /**
     * @return array[]
     */
    public function outputDataProvider()
    {
        return [
            [
                200,
                ["Content-Type" => "application/json"],
                ["test" => "ok"]
            ]
        ];
    }

    /**
     * @dataProvider outputDataProvider
     */
    public function testTransferRequestResponse($statusCode, $headers, $body)
    {
        $output = (new TransportOutput())
            ->hydrate(
                $headers,
                json_encode($body),
                ["http_code" => $statusCode]
            );

        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn($output);

        $response = (new Client())
            ->setTransporter($stubTransport)
            ->transferRequest(new Request(), new ResponseList());

        $this->assertEquals($headers, $response->getResponseHeaders());
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($body, $response->getResponseBody());
    }
}
