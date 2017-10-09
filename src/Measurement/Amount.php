<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-03-04
 * Time: 18:02
 */
declare(strict_types = 1);

namespace Vinnia\Util\Measurement;

use JsonSerializable;

class Amount implements JsonSerializable
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

    /**
     * @param string $unit
     * @return Amount
     */
    public function convertTo(string $unit): self
    {
        $converter = new UnitConverter($this->unit, $unit);
        return new Amount($converter->convert($this->value), $unit);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'unit' => $this->getUnit(),
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param int $decimals
     * @param string $decimalSeparator
     * @param string $thousandsSeparator
     * @return string
     */
    public function format(int $decimals, string $decimalSeparator = '.', string $thousandsSeparator = '')
    {
        return number_format($this->value, $decimals, $decimalSeparator, $thousandsSeparator);
    }

}
