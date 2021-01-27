<?php

namespace Jordy\Http;

interface ClientInterface
{
    public function getRequestPrototype(): RequestInterface;

    public function setRequestPrototype(
        RequestInterface $request
    ): ClientInterface;

    public function getHeaders(): array;

    public function setHeaders(array $headers);

    public function transfer(
        string $httpMethod,
        string $uri,
        array $headers = [],
        $body = null,
        ResponseInterface $response = null
    ): ResponseInterface;

    public function transferEndpoint(
        string $httpMethod,
        EndpointInterface $endpoint
    ): ResponseInterface;

    public function transferRequest(
        RequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface;
}