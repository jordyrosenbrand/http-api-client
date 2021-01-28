<?php

namespace Jordy\Http\Api;

use Jordy\Http\ClientInterface;
use Jordy\Http\Response;
use Jordy\Http\ResponseInterface;
use Jordy\Http\ResponseList;
use Jordy\Http\ResponseListInterface;

abstract class AbstractEndpoint implements EndpointInterface
{
    protected $client;
    protected $uri;
    protected $queryParams = [];
    protected $requestBody;
    protected $responsePrototype;
    protected $responseListPrototype;
    protected $useResponseList = true;
    protected $overwritePrototype = true;

    /**
     * AbstractEndpoint constructor.
     *
     * @param ClientInterface            $client
     * @param ResponseInterface|null     $response
     * @param ResponseListInterface|null $responseList
     */
    public function __construct(
        ClientInterface $client,
        ResponseInterface $response = null,
        ResponseListInterface $responseList = null
    ) {
        $this->client = $client;
        $this->responsePrototype = $response ?? new Response();
        $this->responseListPrototype = $responseList ?? new ResponseList();
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
    public function getRequestBody()
    {
        return $this->requestBody;
    }

    /**
     * @param null $requestBody
     *
     * @return EndpointInterface
     */
    public function withRequestBody($requestBody = null): EndpointInterface
    {
        $clone = clone $this;
        $clone->requestBody = $requestBody;

        return $clone;
    }

    /**
     * @return ResponseInterface
     */
    public function getPrototype(): ResponseInterface
    {
        $prototype = $this->shouldUseResponseList() ?
            $this->getResponseListPrototype() :
            $this->getResponsePrototype();

        return clone $prototype;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponsePrototype(): ResponseInterface
    {
        return $this->responsePrototype;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return EndpointInterface
     */
    public function withResponsePrototype(ResponseInterface $response): EndpointInterface
    {
        $clone = clone $this;
        $clone->responsePrototype = $response;

        return $clone;
    }

    /**
     * @return ResponseListInterface
     */
    public function getResponseListPrototype(): ResponseListInterface
    {
        $list = $this->responseListPrototype;

        if($this->shouldOverwritePrototype()) {
            $list->setPrototype($this->getResponsePrototype());
        }

        return $list;
    }

    /**
     * @param ResponseListInterface $responseList
     *
     * @return EndpointInterface
     */
    public function withResponseListPrototype(ResponseListInterface $responseList): EndpointInterface
    {
        $clone = clone $this;
        $clone->responseListPrototype = $responseList;

        return $clone;
    }

    /**
     * @return $this
     */
    public function returnResponse()
    {
        $this->useResponseList = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function returnResponseList()
    {
        $this->useResponseList = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldUseResponseList(): bool
    {
        return $this->useResponseList;
    }

    /**
     * @return bool
     */
    public function shouldOverwritePrototype(): bool
    {
        return $this->overwritePrototype;
    }

    /**
     * @param bool $overwritePrototype
     *
     * @return $this
     */
    public function setOverwritePrototype(bool $overwritePrototype)
    {
        $this->overwritePrototype = $overwritePrototype;

        return $this;
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
