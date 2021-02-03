<?php declare(strict_types = 1);

namespace Vinnia\Util\Tests\Measurement;

use LogicException;
use Vinnia\Util\Measurement\Kilogram;
use Vinnia\Util\Measurement\Length;
use Vinnia\Util\Measurement\LengthUnit;
use Vinnia\Util\Measurement\Mass;
use Vinnia\Util\Measurement\Quantity;
use Vinnia\Util\Measurement\Centimeter;
use Vinnia\Util\Measurement\Foot;
use Vinnia\Util\Measurement\Gram;
use Vinnia\Util\Measurement\Inch;
use Vinnia\Util\Measurement\Kilometer;
use Vinnia\Util\Measurement\Meter;
use Vinnia\Util\Measurement\Millimeter;
use Vinnia\Util\Measurement\Pound;
use Vinnia\Util\Measurement\Unit;
use Vinnia\Util\Measurement\UnitConverter;
use Vinnia\Util\Measurement\Yard;
use Vinnia\Util\Tests\AbstractTest;

class QuantityTest extends AbstractTest
{
    public function convertUnitsProvider()
    {
        return [
            [Length::class, Centimeter::unit(), Inch::unit(), 5.08, 2.0],
            [Length::class, Foot::unit(), Millimeter::unit(), 3.0, 914.4],
            [Length::class, Meter::unit(), Centimeter::unit(), 5.5, 550],
            [Length::class, Foot::unit(), Inch::unit(), 1.0, 12.0],
            [Length::class, Foot::unit(), Centimeter::unit(), 5.0, 152.4],
            [Length::class, Yard::unit(), Foot::unit(), 1.0, 3.0],
            [Length::class, Yard::unit(), Inch::unit(), 1.0, 36.0],
            [Length::class, Kilometer::unit(), Meter::unit(), 1.0, 1000.0],
            [Mass::class, Gram::unit(), Pound::unit(), 700, 1.5432358],

            // TODO 2021-01-31: not supporting these units atm.
            // ['kg', 'st', 1.0, 0.157473],
            // [Gram::unit(), 'oz', 1000.0, 35.27396],
            // ['t', 'kg', 1.0, 1000.0],
        ];
    }

    /**
     * @dataProvider convertUnitsProvider
     * @param string $clazz
     * @param Unit $from
     * @param Unit $to
     * @param float $value
     * @param float $expected
     */
    public function testConvertUnits(string $clazz, Unit $from, Unit $to, float $value, float $expected)
    {
        $precision = 1e-3;
        $amount = new $clazz($value, $from);
        $this->assertLessThan($precision, abs($expected - $amount->convertTo($to)->getValue()));

        // convert the other way around
        $amount = new $clazz($expected, $to);
        $this->assertLessThan($precision, abs($value - $amount->convertTo($from)->getValue()));
    }

    public function testAddAmountsOfSameUnit()
    {
        $a = new Mass(1.0, Gram::unit());
        $b = new Mass(2.0, Gram::unit());

        $this->assertSame(3.0, $a->add($b)->getValue());
    }

    public function testAddThrowsOnIncompatibleUnits()
    {
        $this->expectException(LogicException::class);

        $a = new Mass(1.0, Gram::unit());
        $b = new Mass(2.0, Kilogram::unit());

        $a->add($b);
    }

    public function testToString()
    {
        $mass = new Mass(1.556, Kilogram::unit());

        $this->assertSame('1.56 kg', (string) $mass);
    }
}
