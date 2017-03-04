<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-03-04
 * Time: 18:02
 */
declare(strict_types = 1);

namespace Vinnia\Util\Measurement;


class Amount
{

    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $unit;

    /**
     * Distance constructor.
     * @param float $value
     * @param string $unit
     */
    function __construct(float $value, string $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    public function convertTo(string $unit): self
    {
        $converter = new UnitConverter($this->unit, $unit);
        return new Amount($converter->convert($this->value), $unit);
    }

}
