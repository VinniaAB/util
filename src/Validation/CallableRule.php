<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 21:01
 */

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
    private $shouldBreakRuleChain;

    /**
     * @var bool
     */
    private $isOptional;

    /**
     * CallableRule constructor.
     * @param callable $callable
     * @param string $errorMessage
     * @param int $priority
     * @param bool $shouldBreakRuleChain
     * @param bool $isOptional
     */
    function __construct(
        callable $callable,
        string $errorMessage,
        int $priority = 100,
        bool $shouldBreakRuleChain = false,
        bool $isOptional = false
    )
    {
        $this->callable = $callable;
        $this->errorMessage = $errorMessage;
        $this->priority = $priority;
        $this->shouldBreakRuleChain = $shouldBreakRuleChain;
        $this->isOptional = $isOptional;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validateValue($value): bool
    {
        return call_user_func($this->callable, $value);
    }

    /**
     * @param string $property
     * @return string
     */
    public function getErrorMessage(string $property): string
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
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    /**
     * If this rule is valid we stop the validation here.
     * @return bool
     */
    public function shouldBreakRuleChain(): bool
    {
        return $this->shouldBreakRuleChain;
    }
}
