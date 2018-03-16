<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 21:01
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;


class CallableRule extends Rule
{

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var bool
     */
    private $breaksRuleChainOnSuccess;

    /**
     * @var bool
     */
    private $yieldsErrors;

    /**
     * CallableRule constructor.
     * @param callable $callable
     * @param string $errorMessage
     * @param int $priority
     * @param bool $breaksRuleChainOnSuccess
     * @param bool $yieldsErrors
     */
    function __construct(
        callable $callable,
        string $errorMessage,
        int $priority = 100,
        bool $breaksRuleChainOnSuccess = false,
        bool $yieldsErrors = true
    )
    {
        $this->callable = $callable;
        $this->errorMessage = $errorMessage;
        $this->priority = $priority;
        $this->breaksRuleChainOnSuccess = $breaksRuleChainOnSuccess;
        $this->yieldsErrors = $yieldsErrors;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function validateValue($value): bool
    {
        return call_user_func($this->callable, $value);
    }

    /**
     * @param string $property
     * @return string
     */
    protected function getErrorMessage(string $property): string
    {
        return sprintf($this->errorMessage, $property);
    }

    /**
     * A higher value means this rule will be executed sooner
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * This rule can only succeed and will not generate any errors if it fails.
     * @return bool
     */
    public function yieldsErrors(): bool
    {
        return $this->yieldsErrors;
    }

    /**
     * If this rule is valid we stop the validation here.
     * @return bool
     */
    public function breaksRuleChainOnSuccess(): bool
    {
        return $this->breaksRuleChainOnSuccess;
    }
}
