<?php

namespace Jordy\Http;

interface TransportInterface
{
    public function transfer(RequestInterface $request): OutputInterface;
}
