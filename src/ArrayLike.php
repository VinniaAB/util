<?php
declare(strict_types = 1);

namespace Vinnia\Util;

use Countable;

abstract class ArrayLike implements Countable
{
    /**
     * @var mixed[]
     */
    protected $values = [];

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->values);
    }

    /**
     * @return mixed[]
     */
    public function values(): array
    {
        return array_values($this->values);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function contains($value): bool
    {
        return in_array($value, $this->values, true);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
