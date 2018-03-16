<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-06-22
 * Time: 14:10
 */

namespace Vinnia\Util\Tests;


use Vinnia\Util\Arrays;

class ArraysTest extends AbstractTest
{

    public function testGetWithNumericKeys()
    {
        $source = [
            [
                'Hello World',
            ],
        ];

        $this->assertEquals('Hello World', Arrays::get($source, '0.0'));
    }

    public function testGetWithStringKeys()
    {
        $source = [
            'one' => [
                'two' => 'Hello World',
            ],
        ];
        $this->assertEquals('Hello World', Arrays::get($source, 'one.two'));
    }

    public function testSetWithNumericKeys()
    {
        $source = [];
        Arrays::set($source, '0.1', 'Hello World');

        $this->assertEquals([0 => [1 => 'Hello World']], $source);
    }

    public function testSetWithStringKeys()
    {
        $source = ['one' => 'two'];
        Arrays::set($source, 'two', 'Hej');

        $this->assertEquals(['one' => 'two', 'two' => 'Hej'], $source);
    }

    public function testFlatten()
    {
        $flat = Arrays::flatten([
            'one' => [
                'two',
                'hi' => [],
            ],
            'three' => [],
            'four' => 'hello',
        ], '.');

        $this->assertEquals([
            'one.0' => 'two',
            'one.hi' => [],
            'three' => [],
            'four' => 'hello',
        ], $flat);
    }

    public function testFlattenKeys()
    {
        $flat = Arrays::flattenKeys([
            'one' => [
                'two',
                'hi' => [],
            ],
            'three' => [],
            'four' => 'hello',
        ], '.');

        $this->assertEquals([
            'one',
            'one.0',
            'one.hi',
            'three',
            'four',
        ], $flat);
    }

}
