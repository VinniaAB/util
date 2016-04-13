<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-15
 * Time: 11:25
 */

namespace Vinnia\Util\Database;

interface DatabaseInterface
{

    /**
     * Execute a non-query statement
     * @param string $sql
     * @param string[] $params
     * @return mixed
     */
    public function execute(string $sql, array $params = []);

    /**
     * Fetch all rows from the specified query
     * @param string $sql
     * @param string[] $params
     * @return string[][]
     */
    public function queryAll(string $sql, array $params = []);

    /**
     * Fetch a single database row
     * @param string $sql
     * @param string[] $params
     * @return string[]
     */
    public function query(string $sql, array $params = []);

}
