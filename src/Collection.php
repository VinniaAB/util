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
    public function map(Closure $func): Collection
    {
        return new Collection(array_map($func, $this->items));
    }

    /**
     * @param Closure $func
     * @return Collection
     */
    public function filter(Closure $func): Collection
    {
        $filtered = array_filter($this->items, $func);
        $values = array_values($filtered);
        return new Collection($values);
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
    public function flatten(): Collection
    {
        $result = [];
        array_walk_recursive($this->items, function ($value) use (&$result) {
            $result[] = $value;
        });
        return new Collection($result);
    }

    /**
     * @param Closure $func
     * @return Collection
     */
    public function flatMap(Closure $func): Collection
    {
        return $this->flatten()->map($func);
    }

    /**
     * @param int $offset
     * @param int|null $length
     * @return Collection
     */
    public function slice(int $offset, ?int $length = null): Collection
    {
        return new Collection(array_slice($this->items, $offset, $length));
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
    public function tail(): Collection
    {
        return $this->slice(1);
    }

    /**
     * @param Closure $func
     * @return Collection
     */
    public function sort(Closure $func): Collection
    {
        $items = $this->slice(0)->value();
        usort($items, $func);
        return new Collection($items);
    }

    /**
     * @return Collection
     */
    public function reverse(): self
    {
        return new self(array_reverse($this->items));
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
     * @return array
     */
    public function value(): array
    {
        return $this->items;
    }

}
