<?php

namespace Jordy\Http;

interface ResponseInterface
{
    public function setRequest(RequestInterface $request): ResponseInterface;

    public function getRequest(): RequestInterface;

    public function setStatusCode(int $statusCode): ResponseInterface;

    public function getStatusCode(): int;

    public function setResponseHeaders($headers): ResponseInterface;

    public function getResponseHeaders(): array;

    public function setResponseBody($body): ResponseInterface;

    public function getResponseBody();

    public function isValid(): bool;

    public function toArray(): array;

    public function extractFromBody($key);
}
