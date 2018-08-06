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

        parent::__construct($errorMessage, $priority, $breaksRuleChainOnSuccess, $yieldsErrors);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function validateValue($value): bool
    {
        return call_user_func($this->callable, $value);
    }

}
