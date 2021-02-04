<?php

namespace Jordy\Http\Tests\Api;

use Jordy\Http\Api\AbstractEndpoint;
use Jordy\Http\Client;
use Jordy\Http\Response;
use Jordy\Http\ResponseList;
use PHPUnit\Framework\TestCase;

class AbstractEndpointTest extends TestCase
{
    /**
     *
     */
    public function testShouldOverwritePrototype()
    {
        $response = new class() extends Response{};
        $responseList = new class() extends ResponseList{};
        $endpoint = $this->getMockForAbstractClass(
            AbstractEndpoint::class,
            [
                new Client(),
                $response,
                $responseList
            ]
        );

        $endpoint->setOverwritePrototype(true);

        $this->assertEquals($responseList, $endpoint->getResponseListPrototype());
        $this->assertEquals($response, $endpoint->getResponseListPrototype()->getPrototype());
    }

    /**
     *
     */
    public function testShouldNotOverwritePrototype()
    {
        $response = new class() extends Response{};
        $responseList = new class() extends ResponseList{};
        $endpoint = $this->getMockForAbstractClass(
            AbstractEndpoint::class,
            [
                new Client(),
                $response,
                $responseList
            ]
        );

        $endpoint->setOverwritePrototype(false);

        $this->assertEquals($responseList, $endpoint->getResponseListPrototype());
        $this->assertNotEquals($response, $endpoint->getResponseListPrototype()->getPrototype());
    }

    /**
     *
     */
    public function testReturnResponse()
    {
        $response = new Response();
        $responseList = new ResponseList();
        $endpoint = $this->getMockForAbstractClass(
            AbstractEndpoint::class,
            [
                new Client(),
                $response,
                $responseList
            ]
        );

        $endpoint->returnResponse();

        $this->assertEquals($response, $endpoint->getPrototype());
    }

    /**
     *
     */
    public function testReturnResponseList()
    {
        $response = new Response();
        $responseList = new ResponseList();
        $endpoint = $this->getMockForAbstractClass(
            AbstractEndpoint::class,
            [
                new Client(),
                $response,
                $responseList
            ]
        );

        $endpoint->returnResponseList();

        $this->assertEquals($responseList, $endpoint->getPrototype());
    }
}
