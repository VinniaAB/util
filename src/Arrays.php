<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-06-22
 * Time: 14:06
 */

namespace Vinnia\Util;


class Arrays
{

    /**
     * Get an array element with dot notation.
     * @param array $source
     * @param string $key
     * @return mixed|null
     */
    public static function get(array $source, string $key)
    {
        $parts = explode('.', $key);
        $slice = $source;
        foreach ($parts as $part) {
            if (!isset($slice[$part])) {
                return null;
            }
            $slice = $slice[$part];
        }
        return $slice;
    }

    /**
     * Set an array element using dot notation
     * @param array $target
     * @param string $key
     * @param mixed $value
     */
    public static function set(array &$target, string $key, $value): void
    {
        $allParts = explode('.', $key);
        $parts = array_slice($allParts, 0, count($allParts) - 1);
        $slice = &$target;
        foreach ($parts as $part) {
            if (!isset($slice[$part])) {
                $slice[$part] = [];
            }
            $slice = &$slice[$part];
        }
        $slice[end($allParts)] = $value;
    }

}
