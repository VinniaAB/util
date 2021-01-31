<?php declare(strict_types=1);

namespace Vinnia\Util\Database;

interface DatabaseInterface
{
    /**
     * Execute a non-query statement
     * @param string $sql
     * @param string[] $params
     * @return mixed
     */
    public function execute(string $sql, array $params = []): bool;

    /**
     * Fetch all rows from the specified query
     * @param string $sql
     * @param string[] $params
     * @return string[][]
     */
    public function queryAll(string $sql, array $params = []): array;

    /**
     * Fetch a single database row
     * @param string $sql
     * @param string[] $params
     * @return string[]
     */
    public function query(string $sql, array $params = []);
}
