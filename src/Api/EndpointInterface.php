<?php

namespace Jordy\Http\Api;

use Jordy\Http\ResponseInterface;
use Jordy\Http\ResponseListInterface;

interface EndpointInterface
{
    public function getUri(): string;

    public function withUri(string $uri): EndpointInterface;

    public function getQueryParams(): array;

    public function withQueryParams(array $queryParams = []): EndpointInterface;

    public function getPostBody();

    public function withPostBody($body = null): EndpointInterface;

    public function getPrototype(): ResponseInterface;

    public function getResponsePrototype(): ResponseInterface;

    public function withResponsePrototype(ResponseInterface $response): EndpointInterface;

    public function getResponseListPrototype(): ResponseListInterface;

    public function withResponseListPrototype(ResponseListInterface $response): EndpointInterface;

    public function transfer(string $httpMethod): ResponseInterface;
}
