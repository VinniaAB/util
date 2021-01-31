<?php declare(strict_types = 1);

namespace Vinnia\Util\Validation;

class SizeRule extends Rule
{
    const COMPARE_LESS_THAN = 'lt';
    const COMPARE_LESS_THAN_OR_EQUAL = 'lte';
    const COMPARE_EQUAL = 'eq';
    const COMPARE_GREATER_THAN = 'gt';
    const COMPARE_GREATER_THAN_OR_EQUAL = 'gte';
    const COMPARE_NOT_EQUAL = 'ne';

    /**
     * @param mixed $value
     * @return int
     */
    protected function getSizeOf($value): int
    {
        $type = gettype($value);

        switch ($type) {
            case 'boolean':
            case 'integer':
            case 'double':
                return (int) $value;
            case 'string':
                return mb_strlen($value, 'utf-8');
        }

        return count($value);
    }

    /**
     * @inheritDoc
     */
    protected function validateValue($value, array $params = []): bool
    {
        $comparison = $params[0];
        $a = $this->getSizeOf($value);
        $b = (int) $params[1];

        switch ($comparison) {
            case static::COMPARE_LESS_THAN:
                return $a < $b;
            case static::COMPARE_LESS_THAN_OR_EQUAL:
                return $a <= $b;
            case static::COMPARE_EQUAL:
                return $a == $b;
            case static::COMPARE_GREATER_THAN:
                return $a > $b;
            case static::COMPARE_GREATER_THAN_OR_EQUAL:
                return $a >= $b;
            case static::COMPARE_NOT_EQUAL:
                return $a != $b;
        }

        return false;
    }
}
