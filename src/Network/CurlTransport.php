<?php

namespace Jordy\Http\Network;

use CurlHandle;
use Jordy\Http\RequestInterface;

class CurlTransport implements TransportInterface
{
    private TransportOutputInterface $output;
    private ?CurlHandle $curlHandle;
    private bool $autoCloseConnection = true;

    /**
     * CurlTransport constructor.
     *
     * @param TransportOutputInterface|null $output
     */
    public function __construct(TransportOutputInterface $output = null)
    {
        $this->setOutput($output ?? new TransportOutput());
    }

    /**
     * @return TransportOutputInterface
     */
    public function getOutput()
    {
        return clone $this->output;
    }

    /**
     * @param TransportOutputInterface $output
     *
     * @return $this
     */
    public function setOutput(TransportOutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param bool $init
     *
     * @return mixed
     */
    public function getCurlHandle(bool $init = true): CurlHandle
    {
        if(! isset($this->curlHandle) && $init) {
            $this->setCurlHandle(curl_init());
        }

        return $this->curlHandle;
    }

    /**
     * @param $curlHandle
     *
     * @return $this
     */
    public function setCurlHandle(CurlHandle $curlHandle): self
    {
        $this->curlHandle = $curlHandle;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAutoCloseConnection(): bool
    {
        return $this->autoCloseConnection;
    }

    /**
     * @param $autoCloseConnection
     *
     * @return $this
     */
    public function setAutoCloseConnection(bool $autoCloseConnection): self
    {
        $this->autoCloseConnection = $autoCloseConnection;

        return $this;
    }

    /**
     * @param RequestInterface $request
     *
     * @return TransportOutputInterface
     */
    public function transfer(RequestInterface $request): TransportOutputInterface
    {
        $headers = [];

        $data = $this->initCurl($request)
            ->setCustomHttpMethod($request)
            ->configureHeaderCallback(
                function($curl, $header) use (&$headers) {
                    $length = strlen($header);
                    $header = explode(':', $header, 2);

                    if(count($header) === 2) {
                        $headers[strtolower(trim($header[0]))] = trim($header[1]);
                    }

                    return $length;
                }
            )
            ->execute();

        $output = $this->getOutput()
            ->hydrate($headers, $data, $this->getInfo());

        $this->close();

        return $output;
    }

    /**
     * @param RequestInterface $request
     *
     * @return $this
     */
    protected function initCurl(RequestInterface $request): self
    {
        curl_setopt_array($this->getCurlHandle(), [
            CURLOPT_URL => $request->getQueriedUri(),
            CURLOPT_HTTPHEADER => $request->getHeaders(),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_POST => false,
            CURLOPT_CUSTOMREQUEST => null,
        ]);

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    protected function configureHeaderCallback(callable $callback): self
    {
        curl_setopt($this->getCurlHandle(), CURLOPT_HEADERFUNCTION, $callback);

        return $this;
    }

    /**
     * @param RequestInterface $request
     *
     * @return $this
     */
    protected function setCustomHttpMethod(RequestInterface $request): self
    {
        $curlHandle = $this->getCurlHandle();

        if($request->isPost()) {
            curl_setopt_array($curlHandle, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $request->getParsedBody()
            ]);
        }

        if($request->isPut()) {
            curl_setopt_array($curlHandle, [
                CURLOPT_CUSTOMREQUEST => $request->getMethod(),
                CURLOPT_POSTFIELDS => $request->getParsedBody()
            ]);
        }

        if($request->isDelete()) {
            curl_setopt_array($curlHandle, [
                CURLOPT_CUSTOMREQUEST => $request->getMethod()
            ]);
        }

        return $this;
    }

    /**
     * @return bool|string
     */
    protected function execute()
    {
        return curl_exec($this->getCurlHandle());
    }

    /**
     * @return mixed
     */
    protected function getInfo(): array
    {
        return curl_getinfo($this->getCurlHandle());
    }

    /**
     * @return $this
     */
    protected function close(): self
    {
        if($this->getAutoCloseConnection()) {
            $this->closeConnection();
        }

        return $this;
    }

    /**
     * @param null $curlHandle
     *
     * @return $this
     */
    private function closeConnection(CurlHandle $curlHandle = null): self
    {
        $curlHandle = $curlHandle ?: $this->getCurlHandle(false);

        if($curlHandle) {
            curl_close($curlHandle);
        }

        $this->curlHandle = null;

        return $this;
    }
}
