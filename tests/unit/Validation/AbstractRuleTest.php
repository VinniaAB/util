<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 17:06
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;


use Vinnia\Util\Tests\AbstractTest;
use Vinnia\Util\Validation\DataSet;
use Vinnia\Util\Validation\RuleInterface;

abstract class AbstractRuleTest extends AbstractTest
{

    /**
     * @return RuleInterface
     */
    abstract public function getRule(): RuleInterface;

    /**
     * @return array
     */
    abstract public function ruleResultProvider(): array;

    /**
     * @dataProvider ruleResultProvider
     * @param array $data
     * @param string $ruleKey
     * @param int $errorCount
     */
    public function testValidatesData(array $data, string $ruleKey, int $errorCount)
    {
        $errors = $this->getRule()->validateRuleKey(new DataSet($data), $ruleKey);
        $this->assertCount($errorCount, $errors);
    }

}
