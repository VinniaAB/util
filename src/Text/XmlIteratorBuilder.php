<?php
declare(strict_types = 1);

namespace Vinnia\Util\Text;

use DOMDocument;
use DOMNode;
use RuntimeException;
use Vinnia\Util\Stack;

/**
 * Class XmlIteratorBuilder
 * @package Vinnia\Util\Text
 *
 * A memory-efficient XML parser that iterates over specific nodes.
 * Usage:
 *
 * $iter = new XmlIteratorBuilder(['a', 'b']);
 *
 * $xml = <<<XML
 * <root>
 *   <a>Yee</a>
 *   <b>Boi</b>
 *   <c>Hello</c>
 * </root>
 * XML;
 *
 * foreach ($iter->iterate($xml) as $node) {
 *   echo $node->nodeName . PHP_EOL; // "a" or "b"
 * }
 *
 */
class XmlIteratorBuilder
{
    /**
     * @var string[]
     */
    protected $nodeNames;

    /**
     * @var int
     */
    protected $bufferSize;

    /**
     * This document is only used to create new nodes.
     *
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var bool
     */
    protected $continue;

    /**
     * @var Stack|null
     */
    protected $stack;

    /**
     * @var DOMNode[]
     */
    protected $nodeBuffer;

    /**
     * XmlNodeIterator constructor.
     * @param string[] $nodeNames indexed by their target XML node
     * @param int $bufferSize
     */
    function __construct(array $nodeNames, int $bufferSize = 8192)
    {
        $this->nodeNames = $nodeNames;
        $this->bufferSize = $bufferSize;
        $this->document = new DOMDocument('1.0', 'utf-8');
        $this->continue = true;
        $this->nodeBuffer = [];
    }

    /**
     * Normalizes the data into a resource stream.
     *
     * @param string|resource $data
     * @return resource
     */
    protected function normalizeData($data)
    {
        if (!is_resource($data)) {
            $handle = fopen('php://memory', 'r+');
            fwrite($handle, (string) $data);
            rewind($handle);
            return $handle;
        }
        return $data;
    }

    /**
     * @param resource $parser
     * @param string $nodeName
     * @param array $attributes
     * @return void
     */
    protected function onStartElement($parser, string $nodeName, array $attributes = []): void
    {
        // if we don't have a callback for the current node
        // and our element stack is empty we can safely
        // disregard this element.
        if (!$this->continue || (!in_array($nodeName, $this->nodeNames) && $this->stack->isEmpty())) {
            return;
        }

        $node = $this->document->createElement($nodeName);

        foreach ($attributes as $name => $value) {
            $node->setAttribute($name, $value);
        }

        // if our element stack is not empty and we found
        // a new element this must be a child of the previous
        // node.
        if (!$this->stack->isEmpty()) {
            $values = $this->stack->values();
            end($values)->appendChild($node);
        }

        $this->stack->push($node);
    }

    /**
     * @param resource $parser
     * @param string $nodeName
     * @return void
     */
    protected function onEndElement($parser, string $nodeName): void
    {
        if (!$this->continue || $this->stack->isEmpty()) {
            return;
        }

        /* @var DOMNode $node */
        $node = $this->stack->pop();

        if (in_array($nodeName, $this->nodeNames)) {
            $this->nodeBuffer[] = $node;
        }
    }

    /**
     * @param resource $parser
     * @param string $data
     * @return void
     */
    protected function onCharacters($parser, string $data): void
    {
        if (!$this->continue || $this->stack->isEmpty()) {
            return;
        }

        $data = trim($data);

        if ($data === '') {
            return;
        }

        $node = $this->document->createTextNode($data);
        $values = $this->stack->values();
        end($values)->appendChild($node);
    }

    /**
     * @param string|resource $data
     * @return iterable
     */
    public function iterate($data): iterable
    {
        $handle = $this->normalizeData($data);
        $this->continue = true;
        $this->nodeBuffer = [];
        $this->stack = new Stack();

        $parser = xml_parser_create('utf-8');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($parser, [$this, 'onStartElement'], [$this, 'onEndElement']);
        xml_set_character_data_handler($parser, [$this, 'onCharacters']);

        try {
            while ($chunk = fread($handle, $this->bufferSize)) {
                $result = xml_parse($parser, $chunk);

                if ($result === 0) {
                    $code = xml_get_error_code($parser);
                    $message = xml_error_string($code);
                    throw new RuntimeException(
                        sprintf('libxml error %d: %s', $code, $message)
                    );
                }

                while ($node = array_shift($this->nodeBuffer)) {
                    yield $node;

                    // it is very important that this check is inside
                    // the while loop. the yield statement above will
                    // pause execution and hand over control to the
                    // user of this iterator. if we don't check immediately
                    // after the yield we may execute too many iterations.
                    if (!$this->continue) {
                        break 2;
                    }
                }
            }
        } finally {
            xml_parse($parser, '', true);
            xml_parser_free($parser);

            // if the $data was originally a string it means
            // we have created a temporary resource. close it.
            if (is_string($data)) {
                fclose($handle);
            }
        }
    }

    /**
     * @return void
     */
    public function stop(): void
    {
        $this->continue = false;
    }
}
