<?php

namespace Jordy\Http;

use Jordy\Http\Api\EndpointInterface;
use Jordy\Http\Network\CurlTransport;
use Jordy\Http\Network\TransportInterface;
use Jordy\Http\Parser\ParserInterface;

class Client implements ClientInterface
{
    const HTTP_GET = "GET";
    const HTTP_POST = "POST";
    const HTTP_PUT = "PUT";
    const HTTP_DELETE = "DELETE";

    private $transport;
    private $requestPrototype;
    private $headers = [];

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->setTransport(new CurlTransport());
        $this->setRequestPrototype(new Request());
    }

    /**
     * @return TransportInterface
     */
    public function getTransport(): TransportInterface
    {
        return $this->transport;
    }

    /**
     * @param TransportInterface $transporter
     *
     * @return $this
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;

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
     * @param ParserInterface $parser
     *
     * @return $this
     */
    public function setParser(ParserInterface $parser)
    {
        $this->setRequestPrototype(
            $this->getRequestPrototype()->setParser($parser)
        );

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
     *
     * @return ResponseInterface
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
     * @param string                 $uri
     * @param array                  $headers
     * @param ResponseInterface|null $responsePrototype
     *
     * @return ResponseInterface
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
     * @return ResponseInterface
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
     * @param string                 $httpMethod
     * @param string                 $uri
     * @param array                  $headers
     * @param null                   $body
     * @param ResponseInterface|null $response
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

        if ($parser = $endpoint->getParser()) {
            $this->setParser($parser);
        }

        $request = $this->getRequestPrototype()
            ->setMethod($httpMethod)
            ->setUri($endpoint->getUri())
            ->setQueryParams($endpoint->getQueryParams())
            ->setHeaders($this->getHeaders())
            ->setBody($endpoint->getRequestBody());

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
        foreach ($this->getHeaders() as $header => $value) {
            $request->addHeader($header, $value);
        }

        $output = $this->getTransport()->transfer($request);

        return $response
            ->setRequest($request)
            ->setStatusCode($output->getStatusCode())
            ->setResponseHeaders($output->getHeaders())
            ->setResponseBody(
                $request->getParser()->decode($output->getBody())
            );
    }
}
