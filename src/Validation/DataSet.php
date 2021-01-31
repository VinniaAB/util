<?php declare(strict_types=1);

namespace Vinnia\Util\Validation;

use Vinnia\Util\Arrays;
use Vinnia\Util\Collection;

class DataSet
{
    const PARENT_KEY_ROOT = '';
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
     * Returns all matching parents for the rule key. Calling
     * this with a key "a.*.b" would return all elements matching
     * the key "a.*". Similarly, calling it with "a.*" would return
     * the element "a". Finally, calling it with "a" would return
     * the whole data set since "a" has no other parent.
     *
     * @param string $ruleKey
     * @return mixed[][]
     */
    public function getParentElements(string $ruleKey): array
    {
        $parentElements = [];

        // first we must determine if this element has a sub-level
        // parent. if it does, extract it. otherwise we can be certain
        // that the parent is actually the complete data set.
        if (preg_match('/^(.*)\.[^\.]+$/', $ruleKey, $matches) === 1) {
            $parentKey = $matches[1];

            foreach ($this->getMatchingKeys($parentKey) as $key) {
                $data = Arrays::get($this->data, $key);

                // this conditional is kind of strange but makes sense
                // when you consider that this is likely a user error.
                // we have successfully found a parent element but it
                // is not an array - something must be very wrong. therefore
                // we implicitly "invalidate" the element and return
                // an empty array instead.
                $parentElements[$key] = is_array($data) ? $data : [];
            }
        } else {
            $parentElements[static::PARENT_KEY_ROOT] = $this->data;
        }

        return $parentElements;
    }
}
