<?php declare(strict_types = 1);

namespace Vinnia\Util\Measurement;

use JsonSerializable;
use RuntimeException;

abstract class Unit implements JsonSerializable
{
    const KIND_LENGTH = 'length';
    const KIND_MASS = 'mass';

    const MILLIMETER = 'mm';
    const CENTIMETER = 'cm';
    const METER = 'm';
    const KILOMETER = 'km';

    const INCH = 'in';
    const FOOT = 'ft';
    const YARD = 'yd';
    const MILE = 'mi';

    const GRAM = 'g';
    const KILOGRAM = 'kg';
    const METRIC_TON = 't';

    const POUND = 'lb';
    const STONE = 'st';
    const OUNCE = 'oz';

    const UNIT_DEFINITIONS = [
        Gram::class => [self::GRAM, 1.0, self::KIND_MASS],
        Kilogram::class => [self::KILOGRAM, 1000.0, self::KIND_MASS],
        Pound::class => [self::POUND, 453.59237, self::KIND_MASS],

        Millimeter::class => [self::MILLIMETER, 0.001, self::KIND_LENGTH],
        Centimeter::class => [self::CENTIMETER, 0.01, self::KIND_LENGTH],
        Meter::class => [self::METER, 1.0, self::KIND_LENGTH],
        Kilometer::class => [self::KILOMETER, 1000.0, self::KIND_LENGTH],

        Inch::class => [self::INCH, 0.0254, self::KIND_LENGTH],
        Foot::class => [self::FOOT, 0.3048, self::KIND_LENGTH],
        Yard::class => [self::YARD, 0.9144, self::KIND_LENGTH],
        Mile::class => [self::MILE, 1609.344, self::KIND_LENGTH],
    ];

    protected string $symbol;
    protected float $SIConversionFactor;
    protected string $kind;

    /**
     * @var Unit[]
     */
    private static array $cache = [];

    protected function __construct(string $symbol, float $SIConversionFactor, string $kind)
    {
        $this->symbol = $symbol;
        $this->SIConversionFactor = $SIConversionFactor;
        $this->kind = $kind;
    }

    public static function unit(): self
    {
        $clazz = static::class;
        if ($clazz === Unit::class) {
            throw new RuntimeException(
                sprintf('\'%s\' may only be called from concrete unit implementations.', __METHOD__)
            );
        }

        if (!isset(static::$cache[$clazz])) {
            [$symbol, $factor, $kind] = static::UNIT_DEFINITIONS[$clazz] ?? null;

            if ($symbol === null) {
                throw new RuntimeException(
                    sprintf('Symbol for unit class \'%s\' is not defined.', $clazz)
                );
            }

            static::$cache[$clazz] = new static($symbol, $factor, $kind);
        }

        return static::$cache[$clazz];
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

    public static function amount(float $value): Amount
    {
        return new Amount($value, static::unit());
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getSIConversionFactor(): float
    {
        return $this->SIConversionFactor;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function jsonSerialize(): string
    {
        return $this->getSymbol();
    }

    public function __toString(): string
    {
        return $this->getSymbol();
    }
}
