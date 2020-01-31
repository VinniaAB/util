<?php
declare(strict_types = 1);

namespace Vinnia\Util\Text;

use RuntimeException;
use XMLReader;

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
     * @var bool
     */
    protected $continue;

    /**
     * XmlNodeIterator constructor.
     * @param string[] $nodeNames indexed by their target XML node
     */
    function __construct(array $nodeNames)
    {
        $this->nodeNames = $nodeNames;
        $this->continue = true;
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
     * @param string|resource $data
     * @return iterable
     */
    public function iterate($data): iterable
    {
        $handle = $this->normalizeData($data);

        // unfortunately we need this ugly tmp-file
        // workaround because XMLReader does not support
        // streams atm.
        $tmp = tmpfile();
        stream_copy_to_stream($handle, $tmp);
        rewind($tmp);

        $meta = stream_get_meta_data($tmp);
        $uri = $meta['uri'];
        $reader = new XMLReader();
        $reader->open($uri, 'utf-8', LIBXML_PARSEHUGE);

        try {
            // suppress errors because XMLReader likes to
            // write junk directly to STDERR.
            while (@$reader->read() && $this->continue) {
                if ($reader->nodeType !== XMLReader::ELEMENT) {
                    continue;
                }

                if (in_array($reader->name, $this->nodeNames)) {
                    yield $reader->expand();
                }
            }
        } finally {
            fclose($tmp);

            // if the $data was originally a string it means
            // we have created a temporary resource. close it.
            if (is_string($data)) {
                fclose($handle);
            }
        }

        if ($err = libxml_get_last_error()) {
            throw new RuntimeException($err->message);
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
