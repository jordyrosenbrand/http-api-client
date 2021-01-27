<?php

namespace Jordy\Http;

class JsonParser implements ParserInterface
{
    /**
     * @param $data
     *
     * @return false|string
     */
    public function encode($data)
    {
        return json_encode($data);
    }

    /**
     * @param $json
     *
     * @return mixed
     */
    public function decode($json)
    {
        return json_decode($json);
    }
}
