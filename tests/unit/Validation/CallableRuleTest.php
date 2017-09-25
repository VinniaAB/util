<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 21:08
 */

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
            [['prop' => true], 'prop', 0],
            [['prop' => false], 'prop', 0],
            [['prop' => 'one'], 'prop', 1],
        ];
    }

}