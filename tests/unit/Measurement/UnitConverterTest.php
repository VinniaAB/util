<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-03-01
 * Time: 21:43
 */
declare(strict_types = 1);

namespace Tests;

use Vinnia\Util\Measurement\Unit;
use Vinnia\Util\Measurement\UnitConverter;
use Vinnia\Util\Tests\AbstractTest;

class UnitConverterTest extends AbstractTest
{

    public function convertUnitsProvider()
    {
        return [
            ['cm', 'in', 5.08, 2.0],
            ['ft', 'mm', 3.0, 914.4],
            ['m', 'cm', 5.5, 550],
            ['ft', 'in', 1.0, 12.0],
            ['ft', 'cm', 5.0, 152.4],
            ['yd', 'ft', 1.0, 3.0],
            ['yd', 'in', 1.0, 36.0],
            ['km', 'm', 1.0, 1000.0],

            ['g', 'lb', 700, 1.5432358],
            ['kg', 'st', 1.0, 0.157473],
            ['g', 'oz', 1000.0, 35.27396],
            ['t', 'kg', 1.0, 1000.0],
            [Unit::SQUARE_FOOT, Unit::SQUARE_METER, 400.0, 37.161216],
            [Unit::METERS_PER_SECOND, Unit::KILOMETERS_PER_HOUR, 2.0, 7.2],
            [Unit::MILES_PER_HOUR, Unit::KILOMETERS_PER_HOUR, 60.0, 96.56],
        ];
    }

    /**
     * @dataProvider convertUnitsProvider
     * @param string $from
     * @param string $to
     * @param float $value
     * @param float $expected
     */
    public function testConvertUnits(string $from, string $to, float $value, float $expected)
    {
        $precision = 1e-3;
        $converter = new UnitConverter($from, $to);
        $this->assertLessThan($precision, abs($expected - $converter->convert($value)));

        // convert the other way around
        $converter = new UnitConverter($to, $from);
        $this->assertLessThan($precision, abs($value - $converter->convert($expected)));
    }

    public function testThrowsOnInvalidConversion()
    {
        $this->expectException(\LogicException::class);
        new UnitConverter('m', 'lb');
    }

}
