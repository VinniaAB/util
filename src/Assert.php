<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-10-09
 * Time: 17:57
 */
declare(strict_types = 1);

namespace Vinnia\Util;


class Assert
{

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws \Exception
     */
    public static function equal($a, $b): void
    {
        if ($a != $b) {
            throw new \Exception(sprintf('Failed asserting that %s is equal to %s', $a, $b));
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws \Exception
     */
    public static function notEqual($a, $b): void
    {
        if ($a == $b) {
            throw new \Exception(sprintf('Failed asserting that %s is equal to %s', $a, $b));
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws \Exception
     */
    public static function greaterThan($a, $b): void
    {
        if ($a <= $b) {
            throw new \Exception(sprintf('Failed asserting that %s is greater than %s', $a, $b));
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws \Exception
     */
    public static function greaterOrEqualThan($a, $b): void
    {
        if ($a < $b) {
            throw new \Exception(sprintf('Failed asserting that %s is greater or equal than %s', $a, $b));
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws \Exception
     */
    public static function lessThan($a, $b): void
    {
        if ($a >= $b) {
            throw new \Exception(sprintf('Failed asserting that %s is less than %s', $a, $b));
        }
    }

    /**
     * @param mixed $a
     * @param mixed $b
     * @throws \Exception
     */
    public static function lessOrEqualThan($a, $b): void
    {
        if ($a > $b) {
            throw new \Exception(sprintf('Failed asserting that %s is less or equal than %s', $a, $b));
        }
    }

}
