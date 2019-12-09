<?php
declare(strict_types = 1);

namespace Vinnia\Util\Text;

use DOMDocument;
use DOMNode;
use RuntimeException;
use Vinnia\Util\Stack;

/**
 * Class XMLCallbackParser
 * @package Vinnia\Util\Text
 *
 * A memory-efficient XML parser that executes a callback function for interesting nodes.
 * Usage:
 *
 * $parser = new XmlCallbackParser([
 *   'a' => function (DOMNode $node) {
 *     echo $node->textContent . PHP_EOL;
 *   },
 *   'b' => function (DOMNode $node) {
 *     echo $node->textContent . PHP_EOL;
 *   },
 * ])
 *
 * $xml = <<<XML
 * <root>
 *   <a>Yee</a>
 *   <b>Boi</b>
 *   <c>Hello</c>
 * </root>
 * XML;
 *
 * $parser->parse($xml);
 *
 */
class XmlCallbackParser
{
    /**
     * @var callable[]
     */
    protected $callbacks;

    /**
     * @var XmlIteratorBuilder
     */
    protected $iterator;

    /**
     * XMLCallbackParser constructor.
     * @param callable[] $callbacks indexed by their target XML node
     * @param int $bufferSize
     */
    function __construct(array $callbacks, int $bufferSize = 8192)
    {
        $this->callbacks = $callbacks;
        $this->iterator = new XmlIteratorBuilder(array_keys($callbacks), $bufferSize);
    }

    /**
     * @param string|resource $data
     * @return void
     */
    public function parse($data): void
    {
        foreach ($this->iterator->iterate($data) as $node) {
            /* @var DOMNode $node */
            call_user_func($this->callbacks[$node->nodeName], $node, $this);
        }
    }

    /**
     * @return void
     */
    public function stop(): void
    {
        $this->iterator->stop();
    }
}
