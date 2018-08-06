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
     * @var string
     */
    protected $errorMessage;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $breaksRuleChainOnSuccess;

    /**
     * @var bool
     */
    protected $yieldsErrors;

    /**
     * Rule constructor.
     * @param string $errorMessage
     * @param int $priority
     * @param bool $breaksRuleChainOnSuccess
     * @param bool $yieldsErrors
     */
    function __construct(
        string $errorMessage,
        int $priority = 100,
        bool $breaksRuleChainOnSuccess = false,
        bool $yieldsErrors = true
    )
    {
        $this->errorMessage = $errorMessage;
        $this->priority = $priority;
        $this->breaksRuleChainOnSuccess = $breaksRuleChainOnSuccess;
        $this->yieldsErrors = $yieldsErrors;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    abstract protected function validateValue($value): bool;

    /**
     * @param string $prop
     * @return string
     */
    protected function getErrorMessage(string $prop): string
    {
        return sprintf($this->errorMessage, $prop);
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return bool
     */
    public function breaksRuleChainOnSuccess(): bool
    {
        return $this->breaksRuleChainOnSuccess;
    }

    /**
     * @return bool
     */
    public function yieldsErrors(): bool
    {
        return $this->yieldsErrors;
    }

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
