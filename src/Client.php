<?php

namespace Jordy\Http;

use Jordy\Http\Api\EndpointInterface;
use Jordy\Http\Network\CurlTransport;
use Jordy\Http\Network\TransportInterface;

class Client implements ClientInterface
{
    const HTTP_GET = "GET";
    const HTTP_POST = "POST";
    const HTTP_PUT = "PUT";
    const HTTP_DELETE = "DELETE";

    private $transporter;
    private $requestPrototype;
    private $headers = [];

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->setTransporter(new CurlTransport());
        $this->setRequestPrototype(new Request());
    }

    /**
     * @return TransportInterface
     */
    public function getTransporter(): TransportInterface
    {
        return $this->transporter;
    }

    /**
     * @param TransportInterface $transporter
     *
     * @return $this
     */
    public function setTransporter(TransportInterface $transporter)
    {
        $this->transporter = $transporter;

        return $this;
    }

    /**
     * @return RequestInterface
     */
    public function getRequestPrototype(): RequestInterface
    {
        return clone $this->requestPrototype;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ClientInterface
     */
    public function setRequestPrototype(
        RequestInterface $request
    ): ClientInterface {
        $this->requestPrototype = $request;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $uri
     * @param array  $headers
     * @param null   $body
     *
     * @return Response
     */
    public function get(string $uri, array $headers = [])
    {
        return $this->transfer(
            self::HTTP_GET,
            $uri,
            $headers,
            null,
            new Response()
        );
    }

    /**
     * @param string $uri
     * @param array  $headers
     * @param null   $body
     *
     * @return ResponseList
     */
    public function getList(
        string $uri,
        array $headers = [],
        ResponseInterface $responsePrototype = null
    ) {
        return $this->transfer(
            self::HTTP_GET,
            $uri,
            $headers,
            null,
            new ResponseList($responsePrototype)
        );
    }

    /**
     * @param string $uri
     * @param array  $headers
     * @param null   $body
     *
     * @return Response
     */
    public function post(string $uri, array $headers = [], $body = null)
    {
        return $this->transfer(
            self::HTTP_POST,
            $uri,
            $headers,
            $body,
            new Response()
        );
    }

    /**
     * @param       $httpMethod
     * @param       $uri
     * @param array $headers
     * @param null  $body
     * @param null  $response
     *
     * @return ResponseInterface
     */
    public function transfer(
        string $httpMethod,
        string $uri,
        array $headers = [],
        $body = null,
        ResponseInterface $response = null
    ): ResponseInterface {
        $response = $response ?? new ResponseList();
        $request = $this->getRequestPrototype()
            ->setMethod($httpMethod)
            ->setUri($uri)
            ->setHeaders($headers)
            ->setBody($body);

        return $this->transferRequest($request, $response);
    }

    /**
     * @param string            $httpMethod
     * @param EndpointInterface $endpoint
     *
     * @return ResponseInterface
     */
    public function transferEndpoint(
        string $httpMethod,
        EndpointInterface $endpoint
    ): ResponseInterface {
        $response = $endpoint->getPrototype();
        $request = $this->getRequestPrototype()
            ->setMethod($httpMethod)
            ->setUri($endpoint->getUri())
            ->setQueryParams($endpoint->getQueryParams())
            ->setHeaders($this->getHeaders())
            ->setBody($endpoint->getPostBody());

        return $this->transferRequest($request, $response);
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function transferRequest(
        RequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $output = $this->getTransporter()->transfer($request);

        return $response
            ->setRequest($request)
            ->setStatusCode($output->getStatusCode())
            ->setResponseHeaders($output->getHeaders())
            ->setResponseBody(
                $request->getParser()->decode($output->getBody())
            );
    }
}
