<?php

namespace Jordy\Http;

interface ResponseInterface
{
    public function getRequest(): RequestInterface;

    public function setRequest(RequestInterface $request): ResponseInterface;

    public function getStatusCode(): int;

    public function setStatusCode(int $statusCode): ResponseInterface;

    public function getResponseHeaders();

    public function setResponseHeaders($headers): ResponseInterface;

    public function getResponseBody();

    public function setResponseBody($body): ResponseInterface;

    public function isValid(): bool;

    public function toArray(): array;
}
