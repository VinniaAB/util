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
     * A higher value means this rule will be executed sooner
     * @return int
     */
    public function getPriority(): int;

    /**
     * Whether the errors of this rule should be included in the resulting error bag
     * @return bool
     */
    public function yieldsErrors(): bool;

    /**
     * If this rule is valid we stop the validation here.
     * @return bool
     */
    public function breaksRuleChainOnSuccess(): bool;

}
