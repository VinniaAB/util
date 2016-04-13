<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-20
 * Time: 13:45
 */

namespace Vinnia\Util\Database;

use PDO;

class PDODatabase implements DatabaseInterface
{

    /**
     * @var PDO
     */
    private $db;

    /**
     * @param PDO $db
     */
    function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @return static
     */
    public static function build($dsn, $username, $password)
    {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        return new static($pdo);
    }

    /**
     * Execute a non-query statement
     * @param string $sql
     * @param string[] $params
     * @return mixed
     */
    public function execute($sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Fetch all rows from the specified query
     * @param string $sql
     * @param string[] $params
     * @return string[][]
     */
    public function queryAll($sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single database row
     * @param string $sql
     * @param string[] $params
     * @return string[]
     */
    public function query($sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
