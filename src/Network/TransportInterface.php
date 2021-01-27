<?php

namespace Jordy\Http\Network;

interface TransportInterface
{
    public function transfer(RequestInterface $request): OutputInterface;
}
