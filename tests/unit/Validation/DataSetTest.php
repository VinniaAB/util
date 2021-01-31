<?php declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;

use Vinnia\Util\Tests\AbstractTest;
use Vinnia\Util\Validation\DataSet;

class DataSetTest extends AbstractTest
{
    /**
     * @return array
     */
    public function parentElementProvider()
    {
        return [
            [
                ['a' => 1], 'a', ['' => ['a' => 1]],
            ],
            [
                [[1], [2], [3]], '*.0', ['0' => [1], '1' => [2], '2' => [3]],
            ],
            [
                ['a' => [1, 2, 3]], 'a.*', ['a' => [1, 2, 3]],
            ],
            [
                ['a' => ['b' => [1, 2, 3], 'c' => [4, 5, 6]]], 'a.*.*', ['a.b' => [1, 2, 3], 'a.c' => [4, 5, 6]],
            ],
        ];
    }

    /**
     * @dataProvider parentElementProvider
     * @param array $data
     * @param string $key
     * @param array $expected
     */
    public function testGetParentElements(array $data, string $key, array $expected)
    {
        $set = new DataSet($data);
        $this->assertEquals($expected, $set->getParentElements($key));
    }

    public function testReturnsEmptyArrayForParentElementWhenNonArrayIsFound()
    {
        $set = new DataSet([
            'a' => 1,
        ]);
        $this->assertEquals(['a' => []], $set->getParentElements('a.*'));
    }
}
