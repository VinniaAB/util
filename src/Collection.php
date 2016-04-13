<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-04-13
 * Time: 22:06
 */

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
     * @return array
     */
    public function value(): array
    {
        return $this->items;
    }

}
