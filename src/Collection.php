<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-04-13
 * Time: 22:06
 */
declare(strict_types = 1);

namespace Vinnia\Util;

use Closure;

class Collection
{

    /**
     * @var array
     */
    private $items;

    /**
     * Collection constructor.
     * @param array $items
     */
    function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param Closure $func
     * @return Collection
     */
    public function map(Closure $func): self
    {
        return new static(array_map($func, $this->items));
    }

    /**
     * @param Closure $func
     * @return Collection
     */
    public function filter(Closure $func): self
    {
        $filtered = array_filter($this->items, $func);
        $values = array_values($filtered);
        return new static($values);
    }

    /**
     * @param Closure $func
     * @return bool
     */
    public function some(Closure $func): bool
    {
        foreach ($this->items as $item) {
            if ($func($item) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Closure $func
     * @return bool
     */
    public function every(Closure $func): bool
    {
        foreach ($this->items as $item) {
            if ($func($item) !== true) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Closure $func
     * @param mixed $initialValue
     * @return mixed
     */
    public function reduce(Closure $func, $initialValue)
    {
        return array_reduce($this->items, $func, $initialValue);
    }

    /**
     * @param string $separator
     * @return string
     */
    public function join(string $separator): string
    {
        return implode($separator, $this->items);
    }

    /**
     * Does not preserve keys.
     * @return Collection
     */
    public function flatten(): self
    {
        $result = [];
        array_walk_recursive($this->items, function ($value) use (&$result) {
            $result[] = $value;
        });
        return new static($result);
    }

    /**
     * @param Closure $func
     * @return Collection
     */
    public function flatMap(Closure $func): self
    {
        return $this->flatten()->map($func);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return Collection
     */
    public function slice(int $offset, ?int $length = null): self
    {
        return new static(array_slice($this->items, $offset, $length));
    }

    /**
     * @return mixed|null
     */
    public function head()
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return Collection
     */
    public function tail(): self
    {
        return $this->slice(1);
    }

    /**
     * @param Closure $func
     * @return Collection
     */
    public function sort(Closure $func): self
    {
        $items = $this->slice(0)->value();
        usort($items, $func);
        return new static($items);
    }

    /**
     * @return Collection
     */
    public function reverse(): self
    {
        return new static(array_reverse($this->items));
    }

    /**
     * @param mixed $value
     * @param bool $strict
     * @return bool
     */
    public function contains($value, bool $strict = false): bool
    {
        return in_array($value, $this->items, $strict);
    }

    /**
     * @return Collection
     */
    public function keys(): self
    {
        return new static(array_keys($this->items));
    }

    /**
     * @param Closure $func
     * @return mixed|null
     */
    public function find(Closure $func)
    {
        foreach ($this->items as $item) {
            if ($func($item) === true) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @return Collection
     */
    public function unique(): self
    {
        return new static(array_values(array_unique($this->items)));
    }

    /**
     * @param Closure $func
     * @return mixed
     */
    public function min(Closure $func)
    {
        $values = $this->map($func)->value();
        return min($values);
    }

    /**
     * @param Closure $func
     * @return mixed
     */
    public function max(Closure $func)
    {
        $values = $this->map($func)->value();
        return max($values);
    }

    /**
     * @param Closure $func
     * @return float
     */
    public function average(Closure $func): float
    {
        $avg = 0.0;
        $len = $this->count();
        foreach ($this->items as $item) {
            $avg += $func($item) / $len;
        }
        return $avg;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function value(): array
    {
        return $this->items;
    }

}
