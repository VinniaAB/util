<?php declare(strict_types=1);

namespace Vinnia\Util\Database;

class MysqlQuoter implements QuoterInterface
{
    /**
     * @param string $name
     * @return mixed
     */
    public function quoteTable(string $name): string
    {
        return "`$name`";
    }

    /**
     * @param string $name
     * @return string
     */
    public function quoteColumn(string $name): string
    {
        return "`$name`";
    }
}
