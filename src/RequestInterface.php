<?php

namespace Jordy\Http;

use Jordy\Http\Parser\ParserInterface;

interface RequestInterface
{
    public function isGet(): bool;

    public function isPost(): bool;

    public function isPut(): bool;

    public function isDelete(): bool;

    public function getMethod(): string;

    public function setMethod(string $method): RequestInterface;

    public function getUri(): string;

    public function setUri(string $uri): RequestInterface;

    public function getQueriedUri(): string;

    public function getQueryParams(): array;

    public function setQueryParams(array $queryParams = []): RequestInterface;

    public function getHeaders(): array;

    public function setHeaders(array $headers): RequestInterface;

    public function getBody();

    public function setBody($body): RequestInterface;

    public function getParsedBody(): string;

    public function getParser(): ParserInterface;

    public function setParser(ParserInterface $parser): RequestInterface;
}
