<?php

namespace Jordy\Http\Tests\Network;

use Jordy\Http\Network\TransportOutput;
use PHPUnit\Framework\TestCase;

class TransportOutputTest extends TestCase
{
    public function hydrateProvider()
    {
        return [
            [
                [
                    "Content-Type" => "application/json"
                ],
                "body",
                [
                    "http_code" => 200
                ],
                200
            ],
            [
                [
                    "Content-Type" => "application/json"
                ],
                "body",
                [],
                0
            ],
        ];
    }

    /**
     * @dataProvider hydrateProvider
     */
    public function testHydrate($headers, $body, $info, $statusCode)
    {
        $transportOutput = (new TransportOutput())
            ->hydrate($headers, $body, $info);

        $this->assertEquals($headers, $transportOutput->getHeaders());
        $this->assertEquals($body, $transportOutput->getBody());
        $this->assertEquals($info, $transportOutput->getInfo());
        $this->assertEquals($statusCode, $transportOutput->getStatusCode());
    }
}
