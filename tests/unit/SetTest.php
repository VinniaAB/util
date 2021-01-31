<?php
declare(strict_types = 1);

namespace Vinnia\Util\Tests;

use PHPUnit\Framework\TestCase;
use Vinnia\Util\Set;

class SetTest extends TestCase
{
    public function testHashesObjects()
    {
        $set = new Set();
        $a = [1];
        $b = [1];

        $set->add($a);

        $this->assertEquals(true, $set->contains($b));
    }

    public function testAddingTheSameValueTwiceDoesNotIncreaseCount()
    {
        $set = new Set(1);

        $this->assertCount(1, $set);

        $set->add(1);

        $this->assertCount(1, $set);
    }

    public function testNullIsUnique()
    {
        $set = new Set(null);

        $this->assertCount(1, $set);
        $set->add(null);

        $this->assertCount(1, $set);
    }

    public function testConstructorUsesHashFunction()
    {
        $set = new Set(1, 2, 3);
        $set->add(1);

        $this->assertCount(3, $set);
    }
}
