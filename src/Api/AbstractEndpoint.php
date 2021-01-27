<?php

namespace Jordy\Http\Api;

abstract class AbstractEndpoint implements EndpointInterface
{
    protected $client;
    protected $uri;
    protected $queryParams = [];
    protected $postBody;
    protected $responsePrototype;

    /**
     * AbstractEndpoint constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return EndpointInterface
     */
    public function withUri(string $uri): EndpointInterface
    {
        $clone = clone $this;
        $clone->uri = $uri;

        return $clone;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @param array $queryParams
     *
     * @return EndpointInterface
     */
    public function withQueryParams(array $queryParams = []): EndpointInterface
    {
        $clone = clone $this;
        $clone->queryParams = $queryParams;

        return $clone;
    }

    /**
     * @return mixed
     */
    public function getPostBody()
    {
        return $this->postBody;
    }

    /**
     * @param $postBody
     *
     * @return AbstractEndpoint
     */
    public function withPostBody($postBody = null): EndpointInterface
    {
        $clone = clone $this;
        $clone->postBody = $postBody;

        return $clone;
    }

    /**
     * @return mixed
     */
    abstract public function getResponsePrototype(): ResponseInterface;

    /**
     * @param ResponseInterface $response
     *
     * @return AbstractEndpoint
     */
    public function withResponsePrototype(ResponseInterface $response): EndpointInterface
    {
        $clone = clone $this;
        $clone->responsePrototype = $response;

        return $clone;
    }

    /**
     * @param string $httpMethod
     *
     * @return ResponseInterface
     */
    public function transfer($httpMethod): ResponseInterface
    {
        return $this->client->transferEndpoint($httpMethod, $this);
    }
}
