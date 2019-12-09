<?php
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Text;


use PHPUnit\Framework\TestCase;
use Vinnia\Util\Text\XmlNodeIterator;

class XmlNodeIteratorTest extends TestCase
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

    public function testIteratesOverSpecifiedNodes()
    {
        $iter = new XmlNodeIterator(['child']);

        foreach ($iter->iterate(static::SOME_XML) as $node) {
            $this->assertEquals('child', $node->nodeName);
        }
    }

    public function testStopsIterating()
    {
        $iter = new XmlNodeIterator(['child']);
        $i = 0;

        foreach ($iter->iterate(static::SOME_XML) as $node) {
            ++$i;
            $iter->stop();
        }

        $this->assertEquals(1, $i);
    }
}
