<?php

namespace Jordy\Http\Tests;

use Jordy\Http\Api\AbstractEndpoint;
use Jordy\Http\Client;
use Jordy\Http\Network\CurlTransport;
use Jordy\Http\Network\TransportOutput;
use Jordy\Http\Parser\JsonParser;
use Jordy\Http\Request;
use Jordy\Http\Response;
use Jordy\Http\ResponseInterface;
use Jordy\Http\ResponseList;
use Jordy\Http\ResponseListInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $transport;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $stubTransport = $this->createMock(CurlTransport::class);
        $stubTransport->method("transfer")
            ->willReturn(new TransportOutput());

        $this->transport = $stubTransport;
    }

    /**
     *
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->transport = null;
    }

    /**
     *
     */
    public function testCombineHeaders()
    {
        $baseHeaders = [
            "Token" => "abc"
        ];
        $requestHeaders = [
            "Content-Type" => "application/json"
        ];

        $request = (new Request())
            ->setHeaders($requestHeaders);

        $response = (new Client())
            ->setTransport($this->transport)
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
     *
     */
    public function testGet()
    {
        $response = (new Client())
            ->setTransport($this->transport)
            ->get("/");

        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertTrue($response->getRequest()->isGet());
    }

    /**
     *
     */
    public function testGetList()
    {
        $response = (new Client())
            ->setTransport($this->transport)
            ->getList("/");

        $this->assertTrue($response instanceof ResponseListInterface);
        $this->assertTrue($response->getRequest()->isGet());
    }

    /**
     *
     */
    public function testPost()
    {
        $response = (new Client())
            ->setTransport($this->transport)
            ->post("/");

        $this->assertTrue($response instanceof ResponseInterface);
        $this->assertTrue($response->getRequest()->isPost());
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
        $client = (new Client())
            ->setTransport($this->transport);

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
        $client = (new Client())
            ->setTransport($this->transport);

        $endpoint = $this->getMockForAbstractClass(
            AbstractEndpoint::class,
            [$client]
        );

        $endpoint = $prototype instanceof ResponseList ?
            $endpoint->withResponseListPrototype($prototype) :
            $endpoint->withResponsePrototype($prototype);

        $endpoint = $endpoint->withUri($uri)
            ->withQueryParams($queryParams)
            ->withRequestBody($body)
            ->withParser(new JsonParser());

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
        $request = (new Request())
            ->setMethod($method)
            ->setUri($uri)
            ->setQueryParams($queryParams);

        $response = (new Client())
            ->setTransport($this->transport)
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
            ->setTransport($stubTransport)
            ->transferRequest(new Request(), new ResponseList());

        $this->assertEquals($headers, $response->getResponseHeaders());
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals($body, $response->getResponseBody());
    }
}
