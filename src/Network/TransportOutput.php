<?php

namespace Jordy\Http\Network;

class TransportOutput implements TransportOutputInterface
{
    private $headers;
    private $body;
    private $info;

    /**
     * TransportOutput constructor.
     *
     * @param array $headers
     * @param null  $body
     * @param array $info
     */
    public function __construct(
        array $headers = [],
        $body = null,
        array $info = []
    ) {
        $this->hydrate($headers, $body, $info);
    }

    /**
     * @param array $headers
     * @param null  $body
     * @param array $info
     *
     * @return TransportOutputInterface
     */
    public function hydrate(
        array $headers = [],
        $body = null,
        array $info = []
    ): TransportOutputInterface {
        $this->headers = $headers;
        $this->body = $body;
        $this->info = $info;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        $info = $this->getInfo();

        return isset($info['http_code']) ? $info['http_code'] : 0;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
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
    public function getInfo()
    {
        return $this->info;
    }
}
