<?php

namespace Jordy\Http\Tests\Parser;

use Jordy\Http\Parser\JsonParser;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            ["string"],
            [["array"]],
            [["array" => "string"]],
            [["array" => ["subArray"]]],
            [["array" => ["subArray" => "string"]]],
            [["array" => ["subArray" => ["item1","item2","item3"]]]]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEncode($data)
    {
        $parser = new JsonParser();
        $expect = json_encode($data);

        $this->assertEquals($expect, $parser->encode($data));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDecode($data)
    {
        $parser = new JsonParser();
        $json = json_encode($data);

        $this->assertEquals($data, $parser->decode($json));
    }
}
