<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-04-14
 * Time: 00:31
 */

namespace Vinnia\Util\Database;


interface QuoterInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function quoteTable(string $name): string;

    /**
     * @param string $name
     * @return string
     */
    public function quoteColumn(string $name): string;

}
