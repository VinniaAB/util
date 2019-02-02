<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2019-02-02
 * Time: 03:12
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;


use Vinnia\Util\Validation\DateRule;
use Vinnia\Util\Validation\RuleInterface;

class DateRuleTest extends AbstractRuleTest
{

    /**
     * @return RuleInterface
     */
    public function getRule(): RuleInterface
    {
        return new DateRule('Y-m-d');
    }

    /**
     * @return array
     */
    public function ruleResultProvider(): array
    {
        return [
            [['yee' => '2019-01-01'], 'yee', 'yee', 0],
            [['yee' => '2019-01-1'], 'yee', 'yee', 0], // not sure why this conforms to Y-m-d.
            [['yee' => '20190101'], 'yee', 'yee', 1],
        ];
    }

}
