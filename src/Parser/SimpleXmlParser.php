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
     * @return bool|string|null
     */
    public function encode($data)
    {
        $simpleXmlElement = $this->arrayToXml($data);

        return $simpleXmlElement ? $simpleXmlElement->asXML() : null;
    }

    /**
     * @param                       $array
     * @param SimpleXMLElement|null $xml
     * @param false                 $collection
     *
     * @return SimpleXMLElement|null
     */
    protected function arrayToXml(
        $array,
        SimpleXMLElement $xml = null,
        $collection = false
    ) {
        if (! $xml instanceof SimpleXMLElement) {
            $xml = new SimpleXMLElement("<{$this->root} />");
        }

        if ($array) {
            foreach ($array as $key => $value) {
                if (is_numeric($key)) {
                    $key = $collection ? $this->element : $xml->getName();
                }

                if (is_array($value)) {
                    $subNode = $xml->addChild($key);
                    $this->arrayToXml($value, $subNode, true);
                } else {
                    $xml->addChild($key, htmlspecialchars($value));
                }
            }
        }

        return $xml;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function decode($contents)
    {
        return $this->xmlToArray(simplexml_load_string($contents));
    }

    /**
     * @param SimpleXMLElement $parent
     *
     * @return array
     */
    protected function xmlToArray(SimpleXMLElement $parent)
    {
        $array = [];

        foreach ($parent as $name => $element) {
            // set array-key by reference
            $name == $this->element ?
                $node = &$array[] :
                $node = &$array[$name];

            // set array-value
            $node = $element->count() ?
                $this->xmlToArray($element) :
                trim($element);
        }

        return $array;
    }
}
