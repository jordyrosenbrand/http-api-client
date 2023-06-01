<?php

namespace Jordy\Http\Parser;

use SimpleXMLElement;

class SimpleXmlParser implements ParserInterface
{
    private string $root;
    private string $element;

    /**
     * @param string $rootName
     * @param string $elementName
     */
    public function __construct(string $rootName = "root", string $elementName = "item")
    {
        $this->root = $rootName;
        $this->element = $elementName;
    }

    /**
     * @param $data
     * @return string
     */
    public function encode($data): string
    {
        return $this->arrayToXml($data)->asXML();
    }

    /**
     * @param array $array
     * @param SimpleXMLElement|null $xml
     * @param bool $collection
     * @return SimpleXMLElement
     */
    protected function arrayToXml(
        array $array,
        SimpleXMLElement $xml = null,
        bool $collection = false
    ): SimpleXMLElement {
        if (! $xml instanceof SimpleXMLElement) {
            $xml = new SimpleXMLElement("<{$this->root} />");
        }

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

        return $xml;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function decode(?string $data)
    {
        return $this->xmlToArray(simplexml_load_string($data));
    }

    /**
     * @param SimpleXMLElement $xml
     *
     * @return array
     */
    protected function xmlToArray(SimpleXMLElement $xml, bool $ignoreIndex = false): array
    {
        $array = [];

        foreach ((array)$xml as $index => $node) {
            $value = $node instanceof SimpleXMLElement ?
                $this->xmlToArray($node, $index !== $node->getName()) :
                $node;

            if ($ignoreIndex || $index === $this->element) {
                $array = $value;
            } else {
                $array[$index] = $value;
            }
        }

        return $array;
    }
}
