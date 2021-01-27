<?php

namespace Jordy\Http;

use ArrayIterator;
use IteratorAggregate;

class ResponseList extends Response implements ResponseInterface, IteratorAggregate
{
    private $prototype;
    private $resultMapping;

    /**
     * ResponseList constructor.
     *
     * @param ResponseInterface|null $prototype
     */
    public function __construct(ResponseInterface $prototype = null)
    {
        $this->setPrototype($prototype ?? new Response());
    }

    /**
     * @return ResponseInterface
     */
    public function getPrototype(): ResponseInterface
    {
        return clone $this->prototype;
    }

    /**
     * @param $prototype
     *
     * @return $this
     */
    public function setPrototype(ResponseInterface $prototype)
    {
        $this->prototype = $prototype;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResultMapping()
    {
        return $this->resultMapping;
    }

    /**
     * @param $resultMapping
     *
     * @return $this
     */
    public function setResultMapping($resultMapping)
    {
        $this->resultMapping = $resultMapping;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $response = $this->getResponseBody();
        $resultMapping = $this->getResultMapping();

        if($resultMapping && $response) {
            if(is_string($resultMapping)) {
                $resultMapping = [$resultMapping];
            }

            foreach($resultMapping as $mapping) {
                $response = $this->extractValue($response, $mapping)
                            ??
                            $response;
            }
        }

        if(is_object($response)) {
            $response = [$response];
        }

        if(is_array($response) && count($response) === 1) {
            $first = reset($response);

            if(! is_object($first) && ! is_array($first)) {
                $response = [$response];
            }
        }

        return is_array($response) ? $response : [];
    }

    /**
     * @param $item
     *
     * @return ResponseInterface
     */
    protected function hydrate($item): ResponseInterface
    {
        return $this->getPrototype()
            ->setRequest($this->getRequest())
            ->setResponseHeaders($this->getResponseHeaders())
            ->setResponseBody($item);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->getItems());
    }

    /**
     * @return ResponseInterface
     */
    public function first(): ResponseInterface
    {
        $items = $this->getItems();

        return $this->hydrate((! empty($items) ? reset($items) : []));
    }

    /**
     * @return ResponseInterface
     */
    public function last(): ResponseInterface
    {
        $items = $this->getItems();

        return $this->hydrate((! empty($items) ? end($items) : []));
    }

    /**
     * @param string $column
     *
     * @return array
     */
    public function column(string $column): array
    {
        $array = [];

        if($items = $this->getItems()) {
            foreach($items as $item) {
                $array[] = $this->extractValue($item, $column);
            }
        }

        return $array;
    }

    /**
     * @param string $keyColumn
     * @param string $valueColumn
     *
     * @return array
     */
    public function map(string $keyColumn, string $valueColumn): array
    {
        $map = [];

        if($items = $this->getItems()) {
            foreach($items as $item) {
                $key = $this->extractValue($item, $keyColumn);
                $value = $this->extractValue($item, $valueColumn);

                $map[$key] = $value;
            }
        }

        return $map;
    }

    /**
     * @param string $column
     * @param        $value
     *
     * @return ResponseInterface|null
     */
    public function find(string $column, $value)
    {
        if($items = $this->getItems()) {
            foreach($items as $item) {
                if($this->extractValue($item, $column) == $value) {
                    return $this->hydrate($item);
                }
            }
        }

        return null;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        if($items = $this->getItems()) {
            foreach($items as $i => $item) {
                if(is_object($item) || is_array($item)) {
                    $items[$i] = $this->hydrate($item);
                }
            }
        }

        return new ArrayIterator($items);
    }

    /**
     * @return array
     */
    public function toNestedArray(): array
    {
        $array = [];

        if($items = $this->getItems()) {
            foreach($items as $item) {
                $array[] = $this->hydrate($item)->toArray();
            }
        }

        return $array;
    }
}
