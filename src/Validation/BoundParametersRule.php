<?php
declare(strict_types = 1);

namespace Vinnia\Util\Validation;

/**
 * A rule with a pre-populated set of parameters.
 *
 * Class BoundParamsRule
 * @package Vinnia\Util\Validation
 */
class BoundParametersRule implements RuleInterface
{
    /**
     * @var RuleInterface
     */
    private $delegate;

    /**
     * @var mixed[]
     */
    private $params;

    /**
     * BoundParamsRule constructor.
     * @param RuleInterface $delegate
     * @param array $params
     */
    function __construct(RuleInterface $delegate, array $params)
    {
        $this->delegate = $delegate;
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    public function validate(DataSet $dataSet, string $ruleKey, ?string $expandedKey, array $params = []): ErrorBag
    {
        return $this->delegate->validate($dataSet, $ruleKey, $expandedKey, array_merge($this->params, $params));
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->delegate->getPriority();
    }

    /**
     * @inheritDoc
     */
    public function yieldsErrors(): bool
    {
        return $this->delegate->yieldsErrors();
    }

    /**
     * @inheritDoc
     */
    public function breaksRuleChainOnSuccess(): bool
    {
        return $this->delegate->breaksRuleChainOnSuccess();
    }
}
