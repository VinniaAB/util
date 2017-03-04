<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-03-01
 * Time: 18:32
 */
declare(strict_types = 1);

namespace Vinnia\Util\Measurement;

use LogicException;

class UnitConverter
{
    const FACTORS_LENGTH_TO_METERS = [
        Unit::MILLIMETER => 0.001,
        Unit::CENTIMETER => 0.01,
        Unit::METER => 1.0,
        Unit::KILOMETER => 1000.0,
        Unit::INCH => 0.0254,
        Unit::FOOT => 0.3048,
        Unit::YARD => 0.9144,
        Unit::MILE => 1609.344,
    ];

    const FACTORS_MASS_TO_GRAMS = [
        Unit::GRAM => 1.0,
        Unit::KILOGRAM => 1000.0,
        Unit::METRIC_TON => 1000000.0,
        Unit::POUND => 453.59237,
        Unit::STONE => 6350.29318,
        Unit::OUNCE => 28.349523125,
    ];

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var float[]
     */
    private $factors;

    /**
     * UnitConverter constructor.
     * @param string $from
     * @param string $to
     */
    function __construct(string $from, string $to)
    {
        $from = mb_strtolower($from, 'utf-8');
        $to = mb_strtolower($to, 'utf-8');

        $tables = [self::FACTORS_LENGTH_TO_METERS, self::FACTORS_MASS_TO_GRAMS];

        // determine what kind of measurement we're converting
        foreach ($tables as $table) {
            if (isset($table[$from], $table[$to])) {
                $this->factors = $table;
                break;
            }
        }

        if (!$this->factors) {
            throw new LogicException(sprintf('Invalid or unsupported conversion of unit "%s" to "%s"', $from, $to));
        }

        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param float $value
     * @return float
     */
    public function convert(float $value): float
    {
        // convert to SI first
        $normalized = $this->factors[$this->from] * $value;

        return $normalized / $this->factors[$this->to];
    }

}
