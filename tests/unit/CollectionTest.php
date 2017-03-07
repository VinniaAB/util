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
    
}
