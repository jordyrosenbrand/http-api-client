<?php

namespace Jordy\Http\Parser;

use SimpleXMLElement;

class SimpleXmlParser implements ParserInterface
{
    private $root;
    private $element;

    /**
     * @param string $rootName
     * @param string $elementName
     */
    public function __construct($rootName = "root", $elementName = "item")
    {
        $this->root = $rootName;
        $this->element = $elementName;
    }

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
    protected function arrayToXml(
        $array,
        SimpleXMLElement $xml = null,
        $collection = false
    ) {
        if (! $xml) {
            $xml = new SimpleXMLElement("<{$this->root} />");
        }

        foreach ($array as $key => $value) {
            $key = is_numeric($key) ?
                ($collection ? $this->element : $xml->getName()) :
                $key;

            if (is_array($value)) {
               $subNode = $xml->addChild($key);

                foreach ($value as $item) {
                    $subNode->addChild($this->element, $item);
                }
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
    protected function xmlToArray(SimpleXMLElement $xml, $ignoreIndex = false)
    {
        $array = [];

        foreach ((array)$xml as $index => $node) {
            $value = $node instanceof SimpleXMLElement ?
                $this->xmlToArray($node, $index == $node->getName()) :
                $node;

            if ($ignoreIndex) {
                $array = $value;
            } else {
                $array[$index] = $value;
            }
        }

        return $array;
    }
}
