<?php declare(strict_types = 1);

namespace Vinnia\Util\Measurement;

use JsonSerializable;
use LogicException;

abstract class Quantity implements JsonSerializable
{
    protected float $value;
    protected Unit $unit;

    public function __construct(float $value, Unit $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return sprintf("%.2f %s", $this->value, $this->unit);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'unit' => $this->unit->getSymbol(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function format(int $decimals, string $decimalSeparator = '.', string $thousandsSeparator = ''): string
    {
        return number_format($this->value, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    public static function assertCompatibleUnits(Quantity $a, Quantity $b): void
    {
        if ($a->unit !== $b->unit) {
            throw new LogicException(
                sprintf('Incompatible units \'%s\' and \'%s\'.', $a->unit, $b->unit)
            );
        }
    }
}
