<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 15:04
 */
declare(strict_types = 1);

namespace Vinnia\Util\Validation;


class RequiredRule implements RuleInterface
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
     * @param null|string $expandedKey
     * @return ErrorBag
     */
    public function validate(DataSet $dataSet, string $ruleKey, ?string $expandedKey): ErrorBag
    {
        $props = $dataSet->getMatchingKeys($ruleKey);
        $bag = new ErrorBag;

        if (empty($props)) {
            $bag->addError($ruleKey, sprintf($this->errorMessage, $ruleKey));
        }

        return $bag;
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
    public function yieldsErrors(): bool
    {
        return true;
    }

    /**
     * If this rule is valid we stop the validation here.
     * @return bool
     */
    public function breaksRuleChainOnSuccess(): bool
    {
        return false;
    }

}
