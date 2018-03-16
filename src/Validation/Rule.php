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
     * @param string $prop
     * @return string
     */
    abstract protected function getErrorMessage(string $prop): string;

    /**
     * @param DataSet $dataSet
     * @param string $ruleKey
     * @return ErrorBag
     */
    public function validateRuleKey(DataSet $dataSet, string $ruleKey): ErrorBag
    {
        $props = $dataSet->getMatchingKeys($ruleKey);
        $errors = new ErrorBag;

        foreach ($props as $prop) {
            if (!$this->validateValue(Arrays::get($dataSet->getData(), $prop))) {
                $errors->addError($prop, $this->getErrorMessage($prop));
            }
        }

        return $errors;
    }

}
