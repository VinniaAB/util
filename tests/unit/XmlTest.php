<?php
declare(strict_types = 1);

namespace Vinnia\Util\Tests;

use Vinnia\Util\Xml;
use PHPUnit\Framework\TestCase;
use DOMDocument;

class XmlTest extends TestCase
{

    public function xmlProvider()
    {
        return [
            [[
                'key' => 'value',
            ], '<key>value</key>'],
            [[
                'a' => [
                    'b' => 'c',
                    'd' => 'e',
                ],
            ], '<a><b>c</b><d>e</d></a>'],
            [[
                'a' => [
                    'one',
                    'two',
                ],
            ], '<a>one</a><a>two</a>'],
        ];
    }

    /**
     * @dataProvider xmlProvider
     * @param array $data
     * @param string $expected
     */
    public function testFromArray(array $data, string $expected)
    {
        $xml = Xml::fromArray($data);

        $this->assertEquals($expected, $xml);
    }

    public function testFromArrayEscapesContent()
    {
        $xml = Xml::fromArray([
            'a' => '<',
        ]);

        $this->assertEquals('<a>&lt;</a>', $xml);
    }

    public function testToArray()
    {
        $xml = <<<EOD
<One>
    <Two>1</Two>
    <Two>2</Two>
    <Two>3</Two>
    <Three>
        <Hello>World</Hello>
        <Hello>World Again</Hello>
    </Three>    
</One>
EOD;
        $el = new DOMDocument('1.0', 'utf-8');
        $el->loadXML($xml);
        $arrayed = Xml::toArray($el);

        $this->assertEquals([
            'Two' => [1, 2, 3],
            'Three' => [
                'Hello' => [
                    'World',
                    'World Again',
                ],
            ],
        ], $arrayed);
    }

    public function testToArraySerializesSingleEmptyElementToString()
    {
        $xml = <<<EOD
<root>
    <name />
</root>
EOD;

        $el = new DOMDocument('1.0', 'utf-8');
        $el->loadXML($xml);
        $arrayed = Xml::toArray($el);

        $this->assertEquals([
            'name' => '',
        ], $arrayed);
    }

    public function testToArraySerializesEmptyElementInArrayToString()
    {
        $xml = <<<EOD
<root>
    <name />
    <name>Hello</name>
</root>
EOD;

        $el = new DOMDocument('1.0', 'utf-8');
        $el->loadXML($xml);
        $arrayed = Xml::toArray($el);

        $this->assertEquals([
            'name' => [
                '',
                'Hello',
            ],
        ], $arrayed);
    }

    public function testToArrayWorksWithSimpleXML()
    {
        $xml = <<<EOD
<root>
    <name />
    <name>Hello</name>
</root>
EOD;
        $el = new \SimpleXMLElement($xml);

        $arrayed = Xml::toArray($el);

        $this->assertEquals([
            'name' => [
                '',
                'Hello',
            ],
        ], $arrayed);
    }
}
