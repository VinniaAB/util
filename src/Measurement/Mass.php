<?php declare(strict_types=1);

namespace Vinnia\Util\Measurement;

final class Mass extends Quantity
{
    public function __construct(float $value, MassUnit $unit)
    {
        parent::__construct($value, $unit);
    }

    public function add(Mass $other): self
    {
        static::assertCompatibleUnits($this, $other);
        return new Mass($this->value + $other->value, $this->unit);
    }

    public function multiply(float $factor): self
    {
        return new Mass($this->value * $factor, $this->unit);
    }

    public function convertTo(MassUnit $other): self
    {
        if ($this->unit === $other) {
            return $this;
        }

        $normalized = $this->value * $this->unit->getSIConversionFactor();
        $converted = $normalized / $other->getSIConversionFactor();
        return new Mass($converted, $other);
    }

    public function getUnit(): MassUnit
    {
        return $this->unit;
    }
}
