<?php declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;

use Vinnia\Util\Tests\AbstractTest;
use Vinnia\Util\Validation\DataSet;
use Vinnia\Util\Validation\Rule;

class RuleTest extends AbstractTest
{
    /**
     * @var Rule
     */
    public $rule;

    public function setUp(): void
    {
        parent::setUp();

        $this->rule = new class('') extends Rule {
            function __construct(string $errorMessage, int $priority = 100, bool $breaksRuleChainOnSuccess = false, bool $yieldsErrors = true)
            {
                parent::__construct($errorMessage, $priority, $breaksRuleChainOnSuccess, $yieldsErrors);
            }

            public function validateValue($value, array $params = []): bool
            {
                return true;
            }
            public function getErrorMessage(string $property, array $params = []): string
            {
                return '';
            }
            public function getPriority(): int
            {
                return 100;
            }
            public function yieldsErrors(): bool
            {
                return false;
            }
            public function breaksRuleChainOnSuccess(): bool
            {
                return false;
            }
        };
    }

    public function testGetMatchingKeysWithDotNotation()
    {
        $data = new DataSet([
            'my.prop' => 1,
            'my.other.prop' => 2,
        ]);
        $props = $data->getMatchingKeys('my.prop');

        $this->assertEquals(['my.prop'], $props);
    }

    public function testGetsMultipleKeysWithWildcard()
    {
        $data = new DataSet([
            'my.first.prop' => 1,
            'my.second.prop' => 2,
            'my.third.prop' => 3,
            'my.four.thing' => 4,
        ]);
        $props = $data->getMatchingKeys('my.*.prop');
        $this->assertEquals(['my.first.prop', 'my.second.prop', 'my.third.prop'], $props);
    }
}
