<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-04-14
 * Time: 00:59
 */

namespace Vinnia\Util\Tests;


use Vinnia\Util\Collection;

class CollectionTest extends AbstractTest
{

    /**
     * @var Collection
     */
    public $collection;

    public function setUp()
    {
        parent::setUp();
        $this->collection = new Collection([0, 1, 2, 3, 4]);
    }

    public function testFilter()
    {
        $c = $this->collection->filter(function($i) { return $i % 2 === 0; });
        $this->assertEquals([0, 2, 4], $c->value());
    }

    public function testMap()
    {
        $c = $this->collection->map(function($i) { return $i * 2; });
        $this->assertEquals([0, 2, 4, 6, 8], $c->value());
    }

    public function testSome()
    {
        $result = $this->collection->some(function($i) { return $i === 0; });
        $this->assertEquals(true, $result);

        $result = $this->collection->some(function($i) { return $i > 4; });
        $this->assertEquals(false, $result);
    }

    public function testEvery()
    {
        $result = $this->collection->every(function($i) { return $i === 0; });
        $this->assertEquals(false, $result);

        $result = $this->collection->every(function($i) { return $i >= 0; });
        $this->assertEquals(true, $result);
    }

    public function testReduce()
    {
        $result = $this->collection->reduce(function($carry, $current) { return $carry + $current; }, 0);
        $this->assertEquals(10, $result);
    }

    public function testJoin()
    {
        $result = $this->collection->join('-');
        $this->assertEquals('0-1-2-3-4', $result);
    }

    public function testFlatten()
    {
        $c = new Collection([[1, 2], [4, 5], [6, 5]]);
        $this->assertEquals([1, 2, 4, 5, 6, 5], $c->flatten()->value());
    }

    public function testFlatMap()
    {
        $c = new Collection([[0, 1], [2, 3]]);
        $result = $c->flatMap(function ($value) { return $value * 2; })->value();
        $this->assertEquals([0, 2, 4, 6], $result);
    }

    public function testSlice()
    {
        $this->assertEquals([0, 1], $this->collection->slice(0, 2)->value());
    }

    public function testHead()
    {
        $this->assertEquals(0, $this->collection->head());
    }

    public function testTail()
    {
        $this->assertEquals([1, 2, 3, 4], $this->collection->tail()->value());
    }

    public function testSort()
    {
        $a = new Collection([3, 2, 1]);

        $b = $a->sort(function (int $a, int $b) {
            return $a <=> $b;
        });

        $this->assertEquals([1, 2, 3], $b->value());

        $c = $a->sort(function (int $a, int $b) {
            return $b <=> $a;
        });

        $this->assertEquals([3, 2, 1], $c->value());
    }

    public function testReverse()
    {
        $a = new Collection(['a', 'b', 'c']);

        $this->assertEquals(['c', 'b', 'a'], $a->reverse()->value());
    }

    public function testContains()
    {
        $a = new Collection(['a', 'b', 'c']);
        $this->assertTrue($a->contains('a'));
        $this->assertFalse($a->contains('d'));
    }

    public function testContainsStrict()
    {
        $a = new Collection([1, 2, 3]);
        $this->assertTrue($a->contains('1', false));
        $this->assertTrue($a->contains(1, true));
        $this->assertFalse($a->contains('1', true));
    }

    public function testKeys()
    {
        $a = new Collection([
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ]);
        $this->assertEquals(['a', 'b', 'c'], $a->keys()->value());
    }

    public function testFind()
    {
        $a = new Collection([1, 2, 3]);
        $value = $a->find(function ($b) { return $b === 1; });
        $this->assertEquals(1, $value);
    }

    public function testFindWithNonExistentValue()
    {
        $a = new Collection([1, 2, 3]);
        $value = $a->find(function ($b) { return $b === 4; });
        $this->assertNull($value);
    }
    
}
