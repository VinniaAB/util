<?php
declare(strict_types = 1);

namespace Vinnia\Util;

use DOMDocument;
use DOMNode;
use DOMText;
use SimpleXMLElement;

class Xml
{
    /**
     * @param DOMNode $node
     * @return bool
     */
    private static function isLeafNode(DOMNode $node): bool
    {
        foreach (($node->childNodes ?? []) as $node) {
            if (!$node instanceof DOMText) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $data
     * @return string
     */
    public static function fromArray(array $data): string
    {
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        $stack = new Stack([$doc, $data]);

        while (!$stack->isEmpty()) {
            /**
             * @var DOMNode $parent
             * @var array $contents
             */
            [$parent, $contents] = $stack->pop();

            foreach ($contents as $key => $value) {
                $values = (array) $value;
                $isNumericArray = Arrays::isNumericKeyArray($values);

                // if the content of this node is indexed
                // by numbers we assume that the user wants
                // to create multiple elements of the same
                // name.
                if ($isNumericArray) {
                    foreach ($values as $v) {
                        $child = $doc->createElement($key);
                        $parent->appendChild($child);

                        if (is_array($v)) {
                            $stack->push([$child, $v]);
                        } else {
                            $child->appendChild(
                                $doc->createTextNode((string) $v)
                            );
                        }
                    }
                }
                // otherwise we push the child values onto
                // the stack and let the next iteration handle
                // the elements.
                else {
                    $child = $doc->createElement($key);
                    $parent->appendChild($child);
                    $stack->push([$child, $values]);
                }
            }
        }

        // we're not really interested in the XML header
        // so let's strip it. we also strip whitespace
        // between tags.
        $xml = $doc->saveXML();
        $xml = mb_substr($xml, 38, null, 'utf-8');
        $xml = preg_replace('/>\s+</', '><', $xml);

        return trim($xml);
    }

    /**
     * @param DOMNode|SimpleXMLElement $xml
     * @return array
     */
    public static function toArray($xml): array
    {
        if (extension_loaded('SimpleXML') && $xml instanceof SimpleXMLElement) {
            $xml = dom_import_simplexml($xml);
        } else if (!$xml instanceof DOMNode) {
            throw new \InvalidArgumentException(
                '$xml must be an instance of either DOMNode or SimpleXMLElement'
            );
        }

        $out = [];

        // notice that we're reaching into the first child
        // of the DOMNode here. this means that the array
        // output will not include the root node.
        $stack = new Stack([$xml->firstChild, &$out]);

        while (!$stack->isEmpty()) {
            /**
             * @var DOMNode $node
             * @var array $chunk
             */
            $parts = $stack->pop();
            $node = $parts[0];
            $chunk = &$parts[1];

            foreach (($node->childNodes ?? []) as $node) {
                $name = $node->nodeName;

                // if the node is not a leaf node (eg. only
                // contains text) we push it onto the stack
                // for later processing. this will have the
                // implicit effect that a node cannot contain
                // both text and elements, but for most cases
                // that's OK.
                if (!static::isLeafNode($node)) {
                    $chunk[$name] = $chunk[$name] ?? [];
                    $stack->push([$node, &$chunk[$name]]);
                    continue;
                }

                $value = $node->nodeValue;

                // skip empty text nodes.
                if ($node instanceof DOMText && trim($value) === '') {
                    continue;
                }

                // if there are multiple instances of this
                // node name we put the values into a numeric
                // array. otherwise we just let the value be
                // a string.
                if (array_key_exists($name, $chunk)) {
                    $chunk[$name] = (array) $chunk[$name];
                    $chunk[$name][] = $value;
                } else {
                    $chunk[$name] = $value;
                }
            }
        }

        return $out;
    }
}
