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
            [['prop' => 'value'], 'key', null, 1],
            [['prop' => 'value'], 'prop', null, 0],
            [['prop.other' => 'value'], 'key', null, 1],
            [['prop.other' => 'value', 'key' => 'otherValue'], 'key', null, 0],
            [['prop.other' => 'value'], 'prop.*', null, 0],
            [['prop.other' => 'value'], 'prop.other', null, 0],
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
