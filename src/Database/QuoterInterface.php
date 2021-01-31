<?php declare(strict_types=1);

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
