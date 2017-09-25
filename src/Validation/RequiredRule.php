<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:04
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;


class RequiredRule extends Rule
{

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * RequiredRule constructor.
     * @param string $errorMessage
     */
    function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param DataSet $dataSet
     * @param string $ruleKey
     * @return ErrorBag
     */
    public function validateRuleKey(DataSet $dataSet, string $ruleKey): ErrorBag
    {
        $props = $this->getMatchingKeys($dataSet->getKeys(), $ruleKey);
        $bag = new ErrorBag;

        if (empty($props)) {
            $bag->addError($ruleKey, $this->getErrorMessage($ruleKey));
        }

        return $bag;
    }

    /**
     * @param array|string $property
     * @return string
     */
    public function getErrorMessage(string $property): string
    {
        return sprintf($this->errorMessage, $property);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validateValue($value): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * This rule can only succeed and will not generate any errors if it fails.
     * @return bool
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * If this rule is valid we stop the validation here.
     * @return bool
     */
    public function shouldBreakRuleChain(): bool
    {
        return false;
    }

}
