<?php declare(strict_types = 1);

namespace Vinnia\Util;

class Arrays
{
    /**
     * Get an array element with dot notation.
     * @param array $source
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    public static function get(array $source, string $key, $default = null)
    {
        $parts = explode('.', $key);
        $slice = $source;
        foreach ($parts as $part) {
            if (!array_key_exists($part, $slice)) {
                return $default;
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

    /**
     * @param array $data
     * @param string $keyDelimiter
     * @return array
     */
    public static function flatten(array $data, string $keyDelimiter = '.'): array
    {
        $flattener = function (array &$out, string $prefix, array $data) use (&$flattener, $keyDelimiter) {
            $prefix = $prefix === '' ? '' : "$prefix$keyDelimiter";
            foreach ($data as $key => $value) {
                if (is_array($value) && !empty($value)) {
                    $flattener($out, "$prefix$key", $value);
                } else {
                    $out["$prefix$key"] = $value;
                }
            }
        };

        $out = [];
        $flattener($out, '', $data);
        return $out;
    }

    /**
     * @param array $data
     * @param string $keyDelimiter
     * @return array
     */
    public static function flattenKeys(array $data, string $keyDelimiter = '.'): array
    {
        $flattener = function (array &$out, string $prefix, array $data) use (&$flattener, $keyDelimiter) {
            $prefix = $prefix === '' ? '' : "$prefix$keyDelimiter";
            foreach ($data as $key => $value) {
                $out[] = $prefix . $key;
                if (is_array($value) && !empty($value)) {
                    $flattener($out, "$prefix$key", $value);
                }
            }
        };
        $out = [];
        $flattener($out, '', $data);
        return $out;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function isNumericKeyArray(array $data): bool
    {
        foreach ($data as $key => $value) {
            if (!is_integer($key)) {
                return false;
            }
        }
        return true;
    }
}
