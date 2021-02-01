<?php declare(strict_types=1);

namespace Vinnia\Util\Measurement;

abstract class MassUnit extends Unit
{
    public function getKind(): string
    {
        return static::KIND_MASS;
    }

    public static function unit(): self
    {
        return static::getCachedUnit(MassUnit::class, static::class);
    }

    public static function mass(float $value): Mass
    {
        return new Mass($value, static::unit());
    }
}
