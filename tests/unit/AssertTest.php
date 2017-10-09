<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-10-09
 * Time: 18:08
 */

namespace Vinnia\Util\Tests;


use Vinnia\Util\Assert;

class AssertTest extends AbstractTest
{

    public function assertionProvider()
    {
        return [
            ['equal', 1, 1, false],
            ['equal', 1, 2, true],
            ['notEqual', 1, 1, true],
            ['notEqual', 1, 2, false],
            ['greaterThan', 2, 1, false],
            ['greaterThan', 1, 1, true],
            ['greaterOrEqualThan', 2, 1, false],
            ['greaterOrEqualThan', 1, 1, false],
            ['greaterOrEqualThan', 1, 1.1, true],
            ['lessThan', 1, 2, false],
            ['lessThan', 1, 0.9, true],
            ['lessOrEqualThan', 1, 2, false],
            ['lessOrEqualThan', 1, 1, false],
            ['lessOrEqualThan', 1, 0.9, true],
        ];
    }

    /**
     * @param string $method
     * @param $a
     * @param $b
     * @param bool $shouldThrow
     * @dataProvider assertionProvider
     */
    public function testAssertions(string $method, $a, $b, bool $shouldThrow)
    {
        if ($shouldThrow) {
            $this->expectException(\Exception::class);
        }

        Assert::$method($a, $b);

        $this->assertTrue(true);
    }

}
