<?php
declare(strict_types = 1);

namespace Tests\Unit;

use Vinnia\Util\Text\XmlCallbackParser;
use Vinnia\Util\Tests\AbstractTest;
use DOMElement;
use RuntimeException;

class XmlCallbackParserTest extends AbstractTest
{
    const SOME_XML = <<<EOD
<root>
  <child a="1" />
  <child />
  <other_child>
    <yee />
  </other_child>
</root>
EOD;

    public function testExecutesCallbacksForNode()
    {
        $called = 0;
        $p = new XmlCallbackParser([
            'child' => function () use (&$called) {
                ++$called;
            },
        ]);
        $p->parse(static::SOME_XML);

        $this->assertEquals(2, $called);
    }

    public function testExecutesCallbackForParentsAndChildren()
    {
        $a = 0;
        $b = 0;
        $p = new XmlCallbackParser([
            'root' => function () use (&$a) {
                ++$a;
            },
            'child' => function () use (&$b) {
                ++$b;
            },
        ]);
        $p->parse(static::SOME_XML);

        $this->assertEquals(1, $a);
        $this->assertEquals(2, $b);
    }

    public function testStopsParsingIfStopIsCalled()
    {
        $a = 0;
        $p = new XmlCallbackParser([
            'child' => function (DOMElement $element, XmlCallbackParser $parser) use (&$a) {
                ++$a;
                $parser->stop();
            },
        ]);
        $p->parse(static::SOME_XML);
        $this->assertEquals(1, $a);
    }

    public function testReturnsCorrectElementInCallback()
    {
        $a = 0;
        $p = new XmlCallbackParser([
            'child' => function (DOMElement $element, XmlCallbackParser $parser) use (&$a) {
                $this->assertEquals('child', $element->nodeName);
                ++$a;
            },
        ]);
        $p->parse(static::SOME_XML);
        $this->assertGreaterThan(0, $a);
    }

    public function testParsedElementIncludesChildNodes()
    {
        $a = 0;
        $p = new XmlCallbackParser([
            'other_child' => function (DOMElement $element, XmlCallbackParser $parser) use (&$a) {
                $this->assertEquals(1, $element->childNodes->length);
                ++$a;
            },
        ]);
        $p->parse(static::SOME_XML);
        $this->assertGreaterThan(0, $a);
    }

    public function testDoesNotExecuteCallbacksForMissingElements()
    {
        $a = 0;
        $p = new XmlCallbackParser([
            'YEE_MY_BOI' => function (DOMElement $element, XmlCallbackParser $parser) use (&$a) {
                ++$a;
            },
        ]);
        $p->parse(static::SOME_XML);
        $this->assertEquals(0, $a);
    }

    public function testRemovesWhitespace()
    {
        $a = 0;
        $p = new XmlCallbackParser([
            'e' => function (DOMElement $element) use (&$a) {
                $this->assertEquals('YEE', $element->textContent);
                ++$a;
            },
        ]);

        $xml = <<<XML
<root>
    <e>  YEE
    
    
    
    </e>
</root>
XML;
        $p->parse($xml);
        $this->assertEquals(1, $a);
    }

    public function testElementsContainsAttributes()
    {
        $a = 0;
        $p = new XmlCallbackParser([
            'child' => function (DOMElement $element, XmlCallbackParser $parser) use (&$a) {
                $this->assertEquals('1', $element->getAttribute('a'));
                $parser->stop();
                ++$a;
            },
        ]);
        $p->parse(static::SOME_XML);
        $this->assertEquals(1, $a);
    }

    public function testParsesResourceData()
    {
        $a = 0;
        $b = 0;
        $p = new XmlCallbackParser([
            'root' => function () use (&$a) {
                ++$a;
            },
            'child' => function () use (&$b) {
                ++$b;
            },
        ]);
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, static::SOME_XML);
        rewind($handle);

        $p->parse($handle);

        $this->assertEquals(1, $a);
        $this->assertEquals(2, $b);
    }

    public function testThrowsOnParseErrors()
    {
        $this->expectException(RuntimeException::class);

        $p = new XmlCallbackParser([]);
        $p->parse(<<<XML
<root>
<yee
</root>
XML
        );
    }
}
