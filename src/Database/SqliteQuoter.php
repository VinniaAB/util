<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-04-14
 * Time: 00:46
 */

namespace Vinnia\Util\Database;


class SqliteQuoter implements QuoterInterface
{

    /**
     * @param string $name
     * @return mixed
     */
    public function quoteTable(string $name): string
    {
        return "\"$name\"";
    }

    /**
     * @param string $name
     * @return string
     */
    public function quoteColumn(string $name): string
    {
        return "\"$name\"";
    }

}
