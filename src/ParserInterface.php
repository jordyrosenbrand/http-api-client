<?php

namespace Jordy\Http;

interface ParserInterface
{
    public function encode($data);

    public function decode($data);
}
