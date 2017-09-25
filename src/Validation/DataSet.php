<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2017-09-10
 * Time: 22:13
 */

namespace Vinnia\Util\Validation;


use Vinnia\Util\Arrays;

class DataSet
{

    /**
     * @var array
     */
    private $data;

    /**
     * @var string[]
     */
    private $keys;

    /**
     * DataSet constructor.
     * @param array $data
     */
    function __construct(array $data)
    {
        $this->data = $data;
        $this->keys = Arrays::flattenKeys($data, '.');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

}