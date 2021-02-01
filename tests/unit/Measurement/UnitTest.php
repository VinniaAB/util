<?php declare(strict_types=1);

namespace Vinnia\Util\Tests\Measurement;

use Exception;
use PHPUnit\Framework\TestCase;
use Vinnia\Util\Measurement\Gram;
use Vinnia\Util\Measurement\Unit;

class UnitTest extends TestCase
{
    public function testStaticUnitCallFromImplementation()
    {
        $first = Gram::unit();
        $second = Gram::unit();

        $this->assertInstanceOf(Gram::class, $first);
        $this->assertSame($first, $second);
    }

    public function testParse()
    {
        $gram = Unit::parse('g');
        $second = Gram::unit();

        $this->assertInstanceOf(Gram::class, $gram);
        $this->assertSame($gram, $second);
    }

    public function testParseThrowsOnUndefinedUnit()
    {
        $this->expectException(Exception::class);

        Unit::parse('doge');
    }
}
