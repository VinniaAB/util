<?php
declare(strict_types = 1);

namespace Vinnia\Util;


class Queue extends ArrayLike
{
    /**
     * Queue constructor.
     * @param mixed ...$values
     */
    function __construct(...$values)
    {
        $this->values = $values;
    }

    /**
     * @param mixed $value
     * @return Queue
     */
    public function push($value): self
    {
        $this->values[] = $value;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function shift()
    {
        return array_shift($this->values);
    }
}
