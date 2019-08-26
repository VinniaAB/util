<?php
declare(strict_types = 1);

namespace Vinnia\Util;

class Set extends ArrayLike
{
    /**
     * Set constructor.
     * @param mixed ...$values
     */
    function __construct(...$values)
    {
        foreach ($values as $value) {
            $this->values[$this->hash($value)] = $value;
        }
    }

    /**
     * @param mixed $value
     * @return self
     */
    public function add($value): self
    {
        $hash = $this->hash($value);
        if (!array_key_exists($hash, $this->values)) {
            $this->values[$hash] = $value;
        }
        return $this;
    }

    /**
     * @param mixed $value
     * @return self
     */
    public function remove($value): self
    {
        $hash = $this->hash($value);
        unset($this->values[$hash]);
        return $this;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function contains($value): bool
    {
        return array_key_exists($this->hash($value), $this->values);
    }

    /**
     * @param mixed $value
     * @return int
     */
    protected function hash($value): int
    {
        return crc32(serialize($value));
    }
}
