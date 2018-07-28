<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-07-28
 * Time: 23:40
 */
declare(strict_types = 1);

namespace Vinnia\Util\Tests\Validation;


use Vinnia\Util\Tests\AbstractTest;
use Vinnia\Util\Validation\DataSet;

class DataSetTest extends AbstractTest
{

    public function testGetMatchingKeys()
    {
        $set = new DataSet([
            'prop' => [
                [
                    'name' => 'yee',
                ],
                [
                    'name' => 'boi'
                ],
                [

                ]
            ],
        ]);

        $keys = $set->getMatchingKeys('prop.*.name');

        $this->assertEquals(['prop.0.name', 'prop.1.name'], $keys);
    }

    public function testGetSizeOfRightmostWildcard()
    {
        $set = new DataSet([
            'prop' => [
                [
                    'name' => 'yee',
                ],
                [
                    'name' => 'boi'
                ],
                [
                ]
            ],
        ]);

        $size = $set->getSizeOfRightmostWildcard('prop.*.name');

        $this->assertEquals(3, $size);
    }

    public function testGetSizeOfRightmostWildcardWithDeepNesting()
    {
        $set = new DataSet([
            'prop' => [
                [
                    'names' => [
                        [
                            'value' => 'yee',
                        ]
                    ],
                ],
                [
                    'names' => [
                        [
                            'value' => 'yee',
                        ]
                    ],
                ],
                [
                    'names' => [
                        [],
                    ],
                ]
            ],
        ]);

        $size = $set->getSizeOfRightmostWildcard('prop.*.names.*.value');

        $this->assertEquals(3, $size);
    }
}
