<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-15
 * Time: 11:27
 */

namespace Vinnia\Util\Database;

use wpdb;

class WordpressDatabase implements DatabaseInterface
{

    /**
     * @var wpdb
     */
    private $db;

    /**
     * @param wpdb $db
     */
    function __construct(wpdb $db)
    {
        $this->db = $db;
    }

    /**
     * Converts a sql-string with pdo-placeholders to its wordpress equivalent
     * Example:
     *     "select * from cars where brand = :brand"
     *  -> "select * from cars where brand = %s"
     * @param string $sql
     * @return string mixed
     */
    private function pdoStringToWpdbString(string $sql)
    {
        return preg_replace('/:\w+/', '%s', $sql);
    }

    /**
     * @param string $sql
     * @param string[] $params
     * @return string
     */
    private function prepare(string $sql, array $params = [])
    {
        $sql = $this->pdoStringToWpdbString($sql);

        // only prepare if placeholder values exist
        if (strpos($sql, '%s') !== false) {

            // wpdb::prepare doesn't actually prepare a statement, it
            // just escapes the values. nice naming schemes yo
            $sql = $this->db->prepare($sql, $params);
        }

        return $sql;
    }

    /**
     * Execute a non-query statement
     * @param string $sql
     * @param string[] $params
     * @return mixed
     */
    public function execute(string $sql, array $params = [])
    {
        $sql = $this->prepare($sql, $params);
        return $this->db->query($sql);
    }

    /**
     * Fetch all rows from the specified query
     * @param string $sql
     * @param string[] $params
     * @return string[][]
     */
    public function queryAll(string $sql, array $params = [])
    {
        $sql = $this->prepare($sql, $params);
        return $this->db->get_results($sql, ARRAY_A);
    }

    /**
     * Fetch a single database row
     * @param string $sql
     * @param string[] $params
     * @return string[]
     */
    public function query(string $sql, array $params = [])
    {
        $sql = $this->prepare($sql, $params);
        return $this->db->get_row($sql, ARRAY_A);
    }
}
