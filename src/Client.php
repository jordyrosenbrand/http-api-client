<?php

namespace Jordy\Http;

use Jordy\Http\Api\EndpointInterface;
use Jordy\Http\Network\CurlTransport;
use Jordy\Http\Network\TransportInterface;
use Jordy\Http\Parser\ParserInterface;

class Client implements ClientInterface
{
    public const HTTP_GET = "GET";
    public const HTTP_POST = "POST";
    public const HTTP_PUT = "PUT";
    public const HTTP_DELETE = "DELETE";

    private TransportInterface $transport;
    private RequestInterface $requestPrototype;
    private array $headers = [];

    /**
     * @param RequestInterface|null $request
     * @param TransportInterface|null $transport
     */
    public function __construct(RequestInterface $request = null, TransportInterface $transport = null)
    {
        $this->setRequestPrototype($request ?? new Request());
        $this->setTransport($transport ?? new CurlTransport());
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
    public function setTransport(TransportInterface $transport): self
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
    public function setRequestPrototype(RequestInterface $request): self
    {
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
    public function setHeaders(array $headers): self
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
    public function get(string $uri, array $headers = []): ResponseInterface
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
    ): ResponseListInterface {
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
    public function post(string $uri, array $headers = [], $body = null): ResponseInterface
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
            ->setBody($endpoint->getRequestBody());

        if($parser = $endpoint->getParser()) {
            $request->setParser($parser);
        }

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
        foreach($this->getHeaders() as $header => $value) {
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
