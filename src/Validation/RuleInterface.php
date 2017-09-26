<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:03
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;


interface RuleInterface
{

    /**
     * @param DataSet $dataSet
     * @param string $ruleKey
     * @return ErrorBag
     */
    public function validateRuleKey(DataSet $dataSet, string $ruleKey): ErrorBag;

    /**
     * @param string $property
     * @return string
     */
    public function getErrorMessage(string $property): string;

    /**
     * A higher value means this rule will be executed sooner
     * @return int
     */
    public function getPriority(): int;

    /**
     * This rule can only succeed and will not generate any errors if it fails.
     * @return bool
     */
    public function isOptional(): bool;

    /**
     * If this rule is valid we stop the validation here.
     * @return bool
     */
    public function shouldBreakRuleChain(): bool;

}