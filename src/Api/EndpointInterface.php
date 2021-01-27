<?php

namespace Jordy\Http\Api;

use Jordy\Http\ResponseInterface;
use Jordy\Http\ResponseListInterface;

interface EndpointInterface
{
    public function getUri(): string;

    public function getQueryParams(): array;

    public function getPostBody();

    public function getPrototype(): ResponseInterface;
}
