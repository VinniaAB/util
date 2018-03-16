<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:44
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;


use Vinnia\Util\Arrays;

abstract class Rule implements RuleInterface
{

    /**
     * @param mixed $value
     * @return bool
     */
    abstract protected function validateValue($value): bool;

    /**
     * @param string[] $keys
     * @param string $ruleKey
     * @return array
     */
    public function getMatchingKeys(array $keys, string $ruleKey): array
    {
        $regex = '/^' . str_replace(['.', '*'], ['\.', '[^\.]+'], $ruleKey) . '$/';
        $props = [];
        foreach ($keys as $key) {
            if (preg_match($regex, $key) === 1) {
                $props[] = $key;
            }
        }
        return $props;
    }

    /**
     * @param DataSet $dataSet
     * @param string $ruleKey
     * @return ErrorBag
     */
    public function validateRuleKey(DataSet $dataSet, string $ruleKey): ErrorBag
    {
        $props = $this->getMatchingKeys($dataSet->getKeys(), $ruleKey);
        $errors = new ErrorBag;

        foreach ($props as $prop) {
            if (!$this->validateValue(Arrays::get($dataSet->getData(), $prop))) {
                $errors->addError($prop, $this->getErrorMessage($prop));
            }
        }

        return $errors;
    }

}
