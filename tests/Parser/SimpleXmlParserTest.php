<?php

namespace Jordy\Http\Tests\Parser;

use Jordy\Http\Parser\SimpleXmlParser;
use PHPUnit\Framework\TestCase;

class SimpleXmlParserTest extends TestCase
{
    /**
     * @return array[]
     */
    public function dataProvider()
    {
        return [
            [
                "<?xml version=\"1.0\"?>\n<root><foo>bar</foo></root>\n",
                ["foo" => "bar"]
            ],
            [
                "<?xml version=\"1.0\"?>\n<data><foo>bar</foo></data>\n",
                ["data" => ["foo" => "bar"]]
            ],
            [
                "<?xml version=\"1.0\"?>\n<root><foo>bar</foo><fruit><item>apple</item><item>orange</item><item>banana</item></fruit></root>\n",
                ["data" => ["foo" => "bar", "fruit" => ["apple", "orange", "banana"]]]
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEncode($string, $array)
    {
        $parser = new SimpleXmlParser();

        $this->assertEquals($string, $parser->encode($array));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDecode($string, $array)
    {
        $parser = new SimpleXmlParser();

        if(count($array) == 1 && is_array(current($array))) {
            $array = current($array);
        }

        $this->assertEquals($array, $parser->decode($string));
    }
}
