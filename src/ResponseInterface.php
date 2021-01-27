<?php

namespace Jordy\Http;

interface ResponseInterface
{
    public function setRequest(RequestInterface $request): ResponseInterface;

    public function setStatusCode(int $statusCode): ResponseInterface;

    public function setResponseHeaders($headers): ResponseInterface;

    public function setResponseBody($body): ResponseInterface;

    public function isValid(): bool;

    public function toArray(): array;
}
