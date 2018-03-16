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
     * @param null|string $expandedKey
     * @return ErrorBag
     */
    public function validate(DataSet $dataSet, string $ruleKey, ?string $expandedKey): ErrorBag
    {
        $errors = new ErrorBag;

        if (!$expandedKey) {
            return $errors;
        }

        $value = Arrays::get($dataSet->getData(), $expandedKey);

        if (!$this->validateValue($value)) {
            $errors->addError($expandedKey, $this->getErrorMessage($expandedKey));
        }

        return $errors;
    }

}
