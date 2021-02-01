<?php declare(strict_types = 1);

namespace Vinnia\Util\Measurement;

use JsonSerializable;
use RuntimeException;

abstract class Unit implements JsonSerializable
{
    const KIND_LENGTH = 'length';
    const KIND_MASS = 'mass';

    const SYMBOL_MILLIMETER = 'mm';
    const SYMBOL_CENTIMETER = 'cm';
    const SYMBOL_METER = 'm';
    const SYMBOL_KILOMETER = 'km';

    const SYMBOL_INCH = 'in';
    const SYMBOL_FOOT = 'ft';
    const SYMBOL_YARD = 'yd';
    const SYMBOL_MILE = 'mi';

    const SYMBOL_GRAM = 'g';
    const SYMBOL_KILOGRAM = 'kg';
    const SYMBOL_METRIC_TON = 't';

    const SYMBOL_POUND = 'lb';
    const SYMBOL_STONE = 'st';
    const SYMBOL_OUNCE = 'oz';

    const UNIT_DEFINITIONS = [
        Gram::class => [self::SYMBOL_GRAM, 1.0],
        Kilogram::class => [self::SYMBOL_KILOGRAM, 1000.0],
        Pound::class => [self::SYMBOL_POUND, 453.59237],

        Millimeter::class => [self::SYMBOL_MILLIMETER, 0.001],
        Centimeter::class => [self::SYMBOL_CENTIMETER, 0.01],
        Meter::class => [self::SYMBOL_METER, 1.0],
        Kilometer::class => [self::SYMBOL_KILOMETER, 1000.0],

        Inch::class => [self::SYMBOL_INCH, 0.0254],
        Foot::class => [self::SYMBOL_FOOT, 0.3048],
        Yard::class => [self::SYMBOL_YARD, 0.9144],
        Mile::class => [self::SYMBOL_MILE, 1609.344],
    ];

    protected string $symbol;
    protected float $SIConversionFactor;

    /**
     * @var Unit[]
     */
    private static array $cache = [];

    protected function __construct(string $symbol, float $SIConversionFactor)
    {
        $this->symbol = $symbol;
        $this->SIConversionFactor = $SIConversionFactor;
    }

    protected static function getCachedUnit(string $baseClazz, string $concreteClazz): self
    {
        if ($baseClazz === $concreteClazz) {
            throw new RuntimeException(
                sprintf('\'%s\' may only be called from concrete unit implementations.', __METHOD__)
            );
        }

        if (!isset(static::$cache[$concreteClazz])) {
            [$symbol, $factor] = static::UNIT_DEFINITIONS[$concreteClazz];
            static::$cache[$concreteClazz] = new static($symbol, $factor);
        }

        return static::$cache[$concreteClazz];
    }

    public static function parse(string $symbol): self
    {
        $symbol = mb_strtolower($symbol, 'utf-8');

        foreach (static::UNIT_DEFINITIONS as $clazz => [$expectedSymbol]) {
            if ($symbol === $expectedSymbol) {
                return $clazz::unit();
            }
        }

        throw new RuntimeException(
            sprintf('Unit class for symbol \'%s\' is not defined.', $symbol)
        );
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getSIConversionFactor(): float
    {
        return $this->SIConversionFactor;
    }

    public function jsonSerialize(): string
    {
        return $this->getSymbol();
    }

    public function __toString(): string
    {
        return $this->getSymbol();
    }

    abstract public function getKind(): string;
}
