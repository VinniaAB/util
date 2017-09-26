<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 20:51
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;


use Vinnia\Util\Tests\AbstractTest;
use Vinnia\Util\Validation\Rule;

class RuleTest extends AbstractTest
{

    /**
     * @var Rule
     */
    public $rule;

    public function setUp()
    {
        parent::setUp();

        $this->rule = new class extends Rule {
            public function validateValue($value): bool
            {
                return true;
            }
            public function getErrorMessage(string $property): string
            {
                return '';
            }
            public function getPriority(): int
            {
                return 100;
            }
            public function isOptional(): bool
            {
                return false;
            }
            public function shouldBreakRuleChain(): bool
            {
                return false;
            }
        };
    }

    public function testGetMatchingKeysWithDotNotation()
    {
        $data = [
            'my.prop',
            'my.other.prop',
        ];
        $props = $this->rule->getMatchingKeys($data, 'my.prop');

        $this->assertEquals(['my.prop'], $props);
    }

    public function testGetsMultipleKeysWithWildcard()
    {
        $data = [
            'my.first.prop',
            'my.second.prop',
            'my.third.prop',
            'my.four.thing',
        ];
        $props = $this->rule->getMatchingKeys($data, 'my.*.prop');
        $this->assertEquals(['my.first.prop', 'my.second.prop', 'my.third.prop'], $props);
    }

}