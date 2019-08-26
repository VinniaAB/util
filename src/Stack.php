<?php
declare(strict_types = 1);

namespace Vinnia\Util;

class Stack extends ArrayLike
{
    /**
     * Stack constructor.
     * @param mixed ...$values
     */
    function __construct(...$values)
    {
        $this->values = $values;
    }

    /**
     * @param $value
     * @return self
     */
    public function push($value): self
    {
        $this->values[] = $value;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function pop()
    {
        return array_pop($this->values);
    }
}
