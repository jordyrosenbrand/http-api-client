<?php

namespace Jordy\Http\Network;

interface TransportOutputInterface
{
    public function hydrate(
        array $headers = [],
        $body = null,
        array $info = []
    ): TransportOutputInterface;

    public function getStatusCode(): int;

    public function getHeaders();

    public function getBody();

    public function getInfo();
}
