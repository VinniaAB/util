<?php declare(strict_types = 1);

namespace Vinnia\Util\Measurement;

use JsonSerializable;
use LogicException;

final class Amount implements JsonSerializable
{
    private float $value;
    private Unit $unit;

    function __construct(float $value, Unit $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }

    public function convertTo(Unit $unit): self
    {
        if ($this->unit->getKind() !== $unit->getKind()) {
            throw new LogicException(
                sprintf('Cannot convert unit \'%s\' into \'%s\'.', $this->unit, $unit)
            );
        }

        $normalized = $this->value * $this->unit->getSIConversionFactor();
        $next = $normalized / $unit->getSIConversionFactor();

        return new Amount($next, $unit);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'unit' => $this->getUnit()->getSymbol(),
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
}
