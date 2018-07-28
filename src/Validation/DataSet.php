<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 22:13
 */

namespace Vinnia\Util\Validation;


use Vinnia\Util\Arrays;
use Vinnia\Util\Collection;

class DataSet
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var string[]
     */
    private $keys;

    /**
     * DataSet constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        $this->data = $data;
        $this->keys = Arrays::flattenKeys($data, '.');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * @param string $ruleKey
     * @return string[]
     */
    public function getMatchingKeys(string $ruleKey): array
    {
        $regex = '/^' . str_replace(['.', '*'], ['\.', '[^\.]+'], $ruleKey) . '$/';
        $keys = [];
        foreach ($this->keys as $key) {
            if (preg_match($regex, $key) === 1) {
                $keys[] = $key;
            }
        }
        return $keys;
    }

    /**
     * @param string $ruleKey
     * @return int
     */
    public function getSizeOfRightmostWildcard(string $ruleKey): int
    {
        if (preg_match('/^(.*)\.\*\.[^\*]+$/', $ruleKey, $matches) === 1) {
            $rightmostKey = $matches[1];

            // the rule key might contain more than
            // one wildcard. therefore we must match
            // our rightmost key against the data and
            // sum the resulting values.
            $matchingSlices = $this->getMatchingKeys($rightmostKey);

            return (new Collection($matchingSlices))->reduce(function (int $carry, string $key): int {
                $data = Arrays::get($this->data, $key);
                return $carry + count($data);
            }, 0);
        }

        return 0;
    }

}
