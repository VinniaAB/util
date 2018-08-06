<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-08-06
 * Time: 14:29
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;

/**
 * Class SizeRule
 * @package Vinnia\Util\Validation
 */
class SizeRule extends Rule
{

    const COMPARE_LESS_THAN = 1;
    const COMPARE_LESS_THAN_OR_EQUAL = 2;
    const COMPARE_EQUAL = 3;
    const COMPARE_GREATER_THAN = 4;
    const COMPARE_GREATER_THAN_OR_EQUAL = 5;

    /**
     * @var int
     */
    protected $comparison;

    /**
     * @var int
     */
    protected $targetSize;

    /**
     * SizeRule constructor.
     * @param int $comparison
     * @param $targetSize
     * @param string $errorMessage
     * @param int $priority
     * @param bool $breaksRuleChainOnSuccess
     * @param bool $yieldsErrors
     */
    function __construct(
        int $comparison,
        int $targetSize,
        string $errorMessage,
        int $priority = 100,
        bool $breaksRuleChainOnSuccess = false,
        bool $yieldsErrors = true
    )
    {
        $this->comparison = $comparison;
        $this->targetSize = $targetSize;

        parent::__construct($errorMessage, $priority, $breaksRuleChainOnSuccess, $yieldsErrors);
    }

    /**
     * @param mixed $value
     * @return int
     */
    protected function getSizeOf($value): int
    {
        $type = gettype($value);

        switch ($type) {
            case 'boolean':
            case 'integer':
            case 'double':
                return (int) $value;
            case 'string':
                return mb_strlen($value, 'utf-8');
        }

        return count($value);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function validateValue($value): bool
    {
        $a = $this->getSizeOf($value);
        $b = $this->targetSize;

        switch ($this->comparison) {
            case static::COMPARE_LESS_THAN:
                return $a < $b;
            case static::COMPARE_LESS_THAN_OR_EQUAL:
                return $a <= $b;
            case static::COMPARE_EQUAL:
                return  $a == $b;
            case static::COMPARE_GREATER_THAN:
                return $a > $b;
            case static::COMPARE_GREATER_THAN_OR_EQUAL:
                return $a >= $b;
        }

        return false;
    }

}
