<?php

namespace Jordy\Http\Parser;

use SimpleXMLElement;

class SimpleXmlParser implements ParserInterface
{
    /**
     * @param $data
     *
     * @return mixed
     */
    public function encode($data)
    {
        return $this->arrayToXml($data)->asXML();
    }

    /**
     * @param SimpleXMLElement $xml
     * @param                  $array
     *
     * @return SimpleXMLElement
     */
    protected function arrayToXml($array, SimpleXMLElement $xml = null)
    {
        if(! $xml) {
            $root = "root";

            if(count($array) == 1 && is_array(current($array))) {
                $root = current(array_keys($array));
                $array = $array[$root];
            }

            $xml = new SimpleXMLElement(
                "<?xml version=\"1.0\"?><{$root}></{$root}>"
            );
        }

        foreach($array as $key => $value) {
            $key = is_numeric($key) ? $xml->getName() : $key;
            if(is_array($value)) {
                $xml->addChild($key, $this->arrayToXml($value, $xml->addChild($key)));
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }

        return $xml;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function decode($data)
    {
        $xml = simplexml_load_string($data);

        return $this->xmlToArray($xml);
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return array
     */
    protected function xmlToArray(SimpleXMLElement $xml)
    {
        $array = [];

        foreach((array)$xml as $index => $node) {
            $array[$index] = is_object($node) ? $this->xmlToArray($node) : $node;
        }

        return $array;
    }
}
