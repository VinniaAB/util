<?php declare(strict_types=1);

namespace Vinnia\Util\Tests\Validation;

use Vinnia\Util\Validation\CallableRule;
use Vinnia\Util\Validation\RuleInterface;

class CallableRuleTest extends AbstractRuleTest
{
    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return new CallableRule('is_bool', 'Hello');
    }

    /**
     * @return array
     */
    public function ruleResultProvider(): array
    {
        return [
            [['prop' => true], 'prop', 'prop', 0],
            [['prop' => false], 'prop', 'prop', 0],
            [['prop' => 'one'], 'prop', 'prop', 1],
        ];
    }
}
