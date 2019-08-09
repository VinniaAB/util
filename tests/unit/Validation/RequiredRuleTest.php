<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 16:01
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;

use Vinnia\Util\Validation\DataSet;
use Vinnia\Util\Validation\RequiredRule;
use Vinnia\Util\Validation\RuleInterface;
use Vinnia\Util\Validation\Validator;

class RequiredRuleTest extends AbstractRuleTest
{

    public function ruleResultProvider(): array
    {
        return [
            [
                ['a' => 'value'], 'b', null, 1,
            ],
            [
                ['a' => 'value'], 'a', null, 0,
            ],
            [
                ['a.b' => 'value'], 'a', null, 1,
            ],
            [
                ['a.b' => 'value', 'c' => 'otherValue'], 'c', null, 0,
            ],
            [
                // this should not error because there
                // are no keys "a.*" in this data set.
                // eg. the parent element is empty.
                ['a.b' => 'value'], 'a.*', null, 0,
            ],
            [
                ['a.b' => 'value'], 'a.b', null, 0,
            ],
            [
                ['prop' => [['name' => 'Hello'], [], []]], 'prop.*.name', null, 2,
            ],
            [
                ['a' => []], 'a.*.a', null, 0,
            ],
            [
                ['a' => []], 'a.*', null, 1,
            ],
            [
                [['a' => 1], ['a' => 2], []], '*.a', null, 1,
            ],
            [
                [], '*', null, 1
            ],
            [
                [], '*.a', null, 0
            ],
        ];
    }

    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return new RequiredRule('');
    }

    public function testDoesNotCrashWhenParentIsNonArray()
    {
        $set = new DataSet([
            'a' => 1,
        ]);

        $rule = new RequiredRule('');
        $bag = $rule->validate($set, 'a.b', null);
        $this->assertEquals(['a.b'], array_keys($bag->getErrors()));
    }

    public function testCorrectlyExpandsWildcardKeyAtRoot()
    {
        $set = new DataSet([
            [],
        ]);

        $rule = new RequiredRule('');
        $bag = $rule->validate($set, '*.a', null);
        $this->assertEquals(['0.a'], array_keys($bag->getErrors()));
    }
}
