<?php
declare(strict_types = 1);

namespace Vinnia\Util\Text;

use DOMDocument;
use DOMNode;
use DOMText;
use DOMElement;
use SimpleXMLElement;
use Vinnia\Util\Arrays;
use Vinnia\Util\Stack;

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
     * @param DOMNode $node
     * @return bool
     */
    private static function isArrayNode(DOMNode $node): bool
    {
        $parent = $node->parentNode;
        if (!$parent instanceof DOMElement) {
            return false;
        }
        foreach (($parent->childNodes ?? []) as $sibling) {
            /* @var DOMNode $sibling */

            // if we find a sibling node with the same name
            // but a different identity we can be sure that
            // this is an array node.
            if ($sibling->nodeName === $node->nodeName && $sibling !== $node) {
                return true;
            }
        }
        return false;
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

        // notice that we're reaching into the first child
        // of the DOMNode here. this means that the array
        // output will not include the root node.
        if ($xml instanceof DOMDocument) {
            $xml = $xml->documentElement;
        }

        $out = [];
        $stack = new Stack([$xml, &$out]);

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
                $value = trim($node->nodeValue);

                // skip empty text nodes.
                if ($node instanceof DOMText && $value === '') {
                    continue;
                }

                // if the node is a leaf node (eg. only
                // contains text) we don't need to operate
                // on its children and we can just save
                // its value.
                if (static::isLeafNode($node)) {
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

                    continue;
                }

                $chunk[$name] = $chunk[$name] ?? [];

                // a node is an "array node" if its parent contains
                // several nodes of the same name. in the following
                // example, "b" is an array node.
                // <a>
                //   <b />
                //   <b />
                // </a>
                $isArrayNode = static::isArrayNode($node);

                if ($isArrayNode) {
                    $chunk[$name][] = [];
                    $stack->push([$node, &$chunk[$name][count($chunk[$name]) - 1]]);
                } else {
                    $stack->push([$node, &$chunk[$name]]);
                }
            }
        }

        return $out;
    }
}
