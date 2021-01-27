<?php

namespace Jordy\Http;

use Jordy\Http\Parser\ParserInterface;

interface RequestInterface
{
    public function isGet(): bool;

    public function isPost(): bool;

    public function isPut(): bool;

    public function isDelete(): bool;

    public function getMethod();

    public function setMethod(string $method): RequestInterface;

    public function getUri();

    public function setUri(string $uri): RequestInterface;

    public function getQueriedUri();

    public function getQueryParams();

    public function setQueryParams(array $queryParams = []): RequestInterface;

    public function getHeaders();

    public function setHeaders(array $headers): RequestInterface;

    public function getBody();

    public function setBody($body): RequestInterface;

    public function getParsedBody();

    public function getParser(): ParserInterface;

    public function setParser(ParserInterface $parser): RequestInterface;
}
