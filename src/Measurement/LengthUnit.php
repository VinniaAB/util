<?php declare(strict_types=1);

namespace Vinnia\Util\Measurement;

abstract class LengthUnit extends Unit
{
    public static function unit(): self
    {
        return static::getCachedUnit(LengthUnit::class, static::class);
    }

    public static function length(float $value): Length
    {
        return new Length($value, static::unit());
    }
}
