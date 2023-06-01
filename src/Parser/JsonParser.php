<?php

namespace Jordy\Http\Parser;

class JsonParser implements ParserInterface
{
    private bool $decodeAssociative;

    /**
     * JsonParser constructor.
     *
     * @param bool $decodeAssociative
     */
    public function __construct(bool $decodeAssociative = true)
    {
        $this->decodeAssociative = $decodeAssociative;
    }

    /**
     * @param $data
     *
     * @return false|string
     */
    public function encode($data): string
    {
        return json_encode($data);
    }

    /**
     * @param $json
     *
     * @return mixed
     */
    public function decode(?string $json)
    {
        return json_decode($json, $this->decodeAssociative);
    }
}
