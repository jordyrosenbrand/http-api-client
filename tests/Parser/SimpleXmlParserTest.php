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
                "<?xml version=\"1.0\"?>\n<root><foo>bar</foo><fruit><item>apple</item><item>orange</item><item>banana</item></fruit></root>\n",
                ["foo" => "bar", "fruit" => ["apple", "orange", "banana"]]
            ],
            [
                "<?xml version=\"1.0\"?>\n<root><foo>bar</foo><food><fruit><item>apple</item><item>orange</item><item>banana</item></fruit></food></root>\n",
                [
                    "foo" => "bar",
                    "food" => ["fruit" => ["apple", "orange", "banana"]]
                ]
            ],
            [
                "<?xml version=\"1.0\"?>\n<root><foo>bar</foo><food><fruit><item>apple</item><item>orange</item><item>banana</item></fruit><vegetable><item>tomato</item><item>carrot</item></vegetable></food></root>\n",
                [
                    "foo" => "bar",
                    "food" => [
                        "fruit" => ["apple", "orange", "banana"],
                        "vegetable" => ["tomato", "carrot"]
                    ]
                ]
            ],
            [
                "<?xml version=\"1.0\"?>\n<root><results><item><type>1</type><id>1</id></item><item><type>2</type><id>2</id></item></results></root>\n",
                [
                    "results" => [
                        ["type" => 1, "id" => 1],
                        ["type" => 2, "id" => 2]
                    ]
                ]
            ],
            [
                "<?xml version=\"1.0\"?>\n<root><results><item><type>1</type></item></results></root>\n",
                ["results" => [["type" => 1]]]
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

        $this->assertEquals($array, $parser->decode($string));
    }
}
