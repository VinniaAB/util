<?php declare(strict_types = 1);

namespace Vinnia\Util\Validation;

use ReflectionFunction;

class CallableRule extends Rule
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var int
     */
    private $numberOfParameters;

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
    ) {
        $this->callable = $callable;
        $this->numberOfParameters = (new ReflectionFunction($callable))->getNumberOfParameters();

        parent::__construct($errorMessage, $priority, $breaksRuleChainOnSuccess, $yieldsErrors);
    }

    /**
     * @inheritDoc
     */
    protected function validateValue($value, array $params = []): bool
    {
        if ($this->numberOfParameters === 1) {
            return call_user_func($this->callable, $value);
        }

        return call_user_func($this->callable, $value, $params);
    }
}
