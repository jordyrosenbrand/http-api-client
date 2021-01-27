<?php

namespace Jordy\Http;

use Jordy\Http\Parser\JsonParser;
use Jordy\Http\Parser\ParserInterface;

class Request implements RequestInterface
{
    private $method;
    private $uri;
    private $queryParams = [];
    private $headers = [];
    private $body;
    private $parser;

    /**
     * Request constructor.
     *
     * @param ParserInterface|null $parser
     */
    public function __construct(ParserInterface $parser = null)
    {
        $this->parser = $parser ?? new JsonParser();
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() == Client::HTTP_GET;
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() == Client::HTTP_POST;
    }

    /**
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->getMethod() == Client::HTTP_PUT;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getMethod() == Client::HTTP_DELETE;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     *
     * @return $this
     */
    public function setMethod($method): RequestInterface
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     *
     * @return $this
     */
    public function setUri($uri): RequestInterface
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getQueriedUri()
    {
        $uri = $this->getUri();

        if($this->getQueryParams()) {
            $uri .= "?" . http_build_query($this->getQueryParams());
        }

        return $uri;
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
     * @return $this
     */
    public function setQueryParams(array $queryParams = []): RequestInterface
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers): RequestInterface
    {
        foreach($headers as $header => $value) {
            $this->addHeader($header, $value);
        }

        return $this;
    }

    /**
     * @param $header
     * @param $value
     *
     * @return $this
     */
    public function addHeader($header, $value)
    {
        $this->headers[$header] = "{$header}: {$value}";

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getParsedBody()
    {
        return $this->getParser()->encode($this->getBody());
    }

    /**
     * @param mixed $body
     *
     * @return $this
     */
    public function setBody($body): RequestInterface
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        return $this->parser;
    }

    /**
     * @param ParserInterface $parser
     *
     * @return $this
     */
    public function setParser(ParserInterface $parser): RequestInterface
    {
        $this->parser = $parser;

        return $this;
    }
}
