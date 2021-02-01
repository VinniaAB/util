<?php declare(strict_types=1);

namespace Vinnia\Util\Measurement;

final class Length extends Quantity
{
    public function __construct(float $value, LengthUnit $unit)
    {
        parent::__construct($value, $unit);
    }

    public function add(Length $other): self
    {
        static::assertCompatibleUnits($this, $other);
        return new Length($this->value + $other->value, $this->unit);
    }

    public function multiply(float $factor): self
    {
        return new Length($this->value * $factor, $this->unit);
    }

    public function convertTo(LengthUnit $other): self
    {
        if ($this->unit === $other) {
            return $this;
        }

        $normalized = $this->value * $this->unit->getSIConversionFactor();
        $converted = $normalized / $other->getSIConversionFactor();
        return new Length($converted, $other);
    }

    public function getUnit(): LengthUnit
    {
        return $this->unit;
    }
}
