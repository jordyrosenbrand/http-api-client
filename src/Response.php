<?php

namespace Jordy\Http;

class Response implements ResponseInterface
{
    private $request;
    private $requestedUri;
    private $requestedBody;

    private $responseHeaders;
    private $statusCode;
    private $responseBody;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return substr($this->getStatusCode(), 0, 1) == 2;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return (array)$this->getResponseBody();
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function setRequest(RequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        $this->requestedUri = $request->getQueriedUri();
        $this->requestedBody = $request->getParsedBody();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @param $headers
     *
     * @return ResponseInterface
     */
    public function setResponseHeaders($headers): ResponseInterface
    {
        $this->responseHeaders = $headers;

        return $this;
    }

    /**
     * @param $header
     *
     * @return mixed|null
     */
    public function getResponseHeader($header)
    {
        return isset($this->getResponseHeaders()[$header]) ?
            $this->getResponseHeaders()[$header] :
            null;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     *
     * @return ResponseInterface
     */
    public function setStatusCode(int $statusCode): ResponseInterface
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return mixed
     */
    final public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param $body
     *
     * @return ResponseInterface
     */
    public function setResponseBody($body): ResponseInterface
    {
        $this->responseBody = $body;

        return $this;
    }

    /**
     * @param $item
     * @param $key
     *
     * @return mixed|null
     */
    protected function extractValue($item, $key)
    {
        if(is_object($item) && property_exists($item, $key)) {
            $value = $item->{$key};
        } elseif(is_array($item) && isset($item[$key])) {
            $value = $item[$key];
        } else {
            $value = null;
        }

        return $value;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function extractFromBody($key)
    {
        $value = $this->getResponseBody();

        if(! is_array($key)) {
            $key = [$key];
        }

        foreach($key as $layer) {
            $value = is_string($layer) ?
                $this->extractValue($value, $layer) :
                null;
        }

        return $value;
    }
}
