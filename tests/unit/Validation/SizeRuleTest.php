<?php declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;

use PHPUnit\Framework\TestCase;
use Vinnia\Util\Validation\DataSet;
use Vinnia\Util\Validation\RuleInterface;
use Vinnia\Util\Validation\SizeRule;

class SizeRuleTest extends TestCase
{
    /**
     * @return array
     */
    public function ruleResultProvider(): array
    {
        return [
            // integers
            [SizeRule::COMPARE_LESS_THAN, 3, [4], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN, 3, [3], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN, 3, [2], '*', '0', 0],

            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, [4], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, [3], '*', '0', 0],
            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, [2], '*', '0', 0],

            [SizeRule::COMPARE_EQUAL, 3, [2], '*', '0', 1],
            [SizeRule::COMPARE_EQUAL, 3, [3], '*', '0', 0],
            [SizeRule::COMPARE_EQUAL, 3, [4], '*', '0', 1],

            [SizeRule::COMPARE_GREATER_THAN, 3, [2], '*', '0', 1],
            [SizeRule::COMPARE_GREATER_THAN, 3, [3], '*', '0', 1],
            [SizeRule::COMPARE_GREATER_THAN, 3, [4], '*', '0', 0],

            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, [2], '*', '0', 1],
            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, [3], '*', '0', 0],
            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, [4], '*', '0', 0],

            // strings
            [SizeRule::COMPARE_LESS_THAN, 3, ['ABCD'], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN, 3, ['ABC'], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN, 3, ['AB'], '*', '0', 0],

            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, ['ABCD'], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, ['ABC'], '*', '0', 0],
            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, ['AB'], '*', '0', 0],

            [SizeRule::COMPARE_EQUAL, 3, ['ABCD'], '*', '0', 1],
            [SizeRule::COMPARE_EQUAL, 3, ['ABC'], '*', '0', 0],
            [SizeRule::COMPARE_EQUAL, 3, ['AB'], '*', '0', 1],

            [SizeRule::COMPARE_GREATER_THAN, 3, ['ABCD'], '*', '0', 0],
            [SizeRule::COMPARE_GREATER_THAN, 3, ['ABC'], '*', '0', 1],
            [SizeRule::COMPARE_GREATER_THAN, 3, ['AB'], '*', '0', 1],

            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, ['ABCD'], '*', '0', 0],
            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, ['ABC'], '*', '0', 0],
            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, ['AB'], '*', '0', 1],

            // arrays
            [SizeRule::COMPARE_LESS_THAN, 3, [[1, 2, 3, 4]], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN, 3, [[1, 2, 3]], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN, 3, [[1, 2]], '*', '0', 0],

            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, [[1, 2, 3, 4]], '*', '0', 1],
            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, [[1, 2, 3]], '*', '0', 0],
            [SizeRule::COMPARE_LESS_THAN_OR_EQUAL, 3, [[1, 2]], '*', '0', 0],

            [SizeRule::COMPARE_EQUAL, 3, [[1, 2, 3, 4]], '*', '0', 1],
            [SizeRule::COMPARE_EQUAL, 3, [[1, 2, 3]], '*', '0', 0],
            [SizeRule::COMPARE_EQUAL, 3, [[1, 2]], '*', '0', 1],

            [SizeRule::COMPARE_GREATER_THAN, 3, [[1, 2, 3, 4]], '*', '0', 0],
            [SizeRule::COMPARE_GREATER_THAN, 3, [[1, 2, 3]], '*', '0', 1],
            [SizeRule::COMPARE_GREATER_THAN, 3, [[1, 2]], '*', '0', 1],

            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, [[1, 2, 3, 4]], '*', '0', 0],
            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, [[1, 2, 3]], '*', '0', 0],
            [SizeRule::COMPARE_GREATER_THAN_OR_EQUAL, 3, [[1, 2]], '*', '0', 1],
        ];
    }

    /**
     * @dataProvider ruleResultProvider
     * @param int $comparison
     * @param int $compareTo
     * @param array $data
     * @param string $ruleKey
     * @param null|string $expandedKey
     * @param int $errorCount
     */
    public function testSizeRule(string $comparison, int $compareTo, array $data, string $ruleKey, ?string $expandedKey, int $errorCount)
    {
        $rule = new SizeRule('Prop: {{property}}, Comparison: {{param_0}}, Target: {{param_1}}');
        $bag = $rule->validate(new DataSet($data), $ruleKey, $expandedKey, [$comparison, $compareTo]);
        $this->assertCount($errorCount, $bag);
    }
}
