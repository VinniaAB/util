<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 16:01
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;

use Vinnia\Util\Validation\RequiredRule;
use Vinnia\Util\Validation\RuleInterface;

class RequiredRuleTest extends AbstractRuleTest
{

    public function ruleResultProvider(): array
    {
        return [
            [['prop' => 'value'], 'key', 1],
            [['prop' => 'value'], 'prop', 0],
            [['prop.other' => 'value'], 'key', 1],
            [['prop.other' => 'value', 'key' => 'otherValue'], 'key', 0],
            [['prop.other' => 'value'], 'prop.*', 0],
            [['prop.other' => 'value'], 'prop.other', 0],
        ];
    }

    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return new RequiredRule('');
    }

}
