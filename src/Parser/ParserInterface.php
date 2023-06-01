<?php

namespace Jordy\Http\Parser;

interface ParserInterface
{
    public function encode($data): string;

    public function decode(?string $data);
}
