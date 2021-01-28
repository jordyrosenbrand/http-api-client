<?php

namespace Jordy\Http;

use Jordy\Http\Api\EndpointInterface;

interface ClientInterface
{
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
