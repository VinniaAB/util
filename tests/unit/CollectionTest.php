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
    
}
