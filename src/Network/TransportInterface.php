<?php

namespace Jordy\Http\Network;

use Jordy\Http\RequestInterface;

interface TransportInterface
{
    public function transfer(RequestInterface $request): TransportOutputInterface;
}
