<?php

namespace Jordy\Http\Parser;

interface ParserInterface
{
    public function encode($data);

    public function decode($data);
}
