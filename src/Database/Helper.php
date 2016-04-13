<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 15-10-27
 * Time: 12:14
 */

namespace Vinnia\Util\Database;


class Helper
{

    /**
     * @var DatabaseInterface
     */
    private $db;

    /**
     * @var QuoterInterface
     */
    private $quoter;

    /**
     * @param DatabaseInterface $db
     * @param QuoterInterface $quoter
     */
    function __construct(DatabaseInterface $db, QuoterInterface $quoter)
    {
        $this->db = $db;
        $this->quoter = $quoter;
    }

    /**
     * @param string[] $predicate
     * @param string[] $paramValues inject parameter values into this array
     * @return string
     */
    protected function buildPredicate(array $predicate, array &$paramValues)
    {
        if (count($predicate) === 0) {
            return '';
        }

        $wheres = [];
        $i = 0;
        foreach ($predicate as $key => $value) {
            $paramName = ':qq' . $i;
            $wheres[] = $this->quoter->quoteColumn($key) . ' = ' . $paramName;
            $paramValues[$paramName] = $value;
            $i++;
        }

        return 'where ' . implode(' AND ', $wheres);
    }

    /**
     * Insert into a table.
     *
     * $helper->insert('cars', ['make' => 'volvo', 'model' => 'xc90']);
     * z
     * @param string $table
     * @param string[] $values
     * @return mixed
     */
    public function insert(string $table, array $values)
    {
        $cols = array_keys($values);
        $cols = array_map(function($col) { return $this->quoter->quoteColumn($col); }, $cols);
        $colString = implode(',', $cols);

        $params = [];
        $paramValues = [];
        $i = 0;
        foreach ($values as $key => $value) {
            $paramName = ':qp' . $i;
            $params[] = $paramName;
            $paramValues[$paramName] = $value;
            $i++;
        }

        $table = $this->quoter->quoteTable($table);
        $paramString = implode(',', $params);
        $sql = "insert into {$table}($colString) values ($paramString)";

        return $this->db->execute($sql, $paramValues);
    }

    /**
     * Update one or multiple rows in a table
     *
     * $helper->update('cars', ['model' => 'v70'], ['make' => 'volvo']);
     *
     * @param string $table
     * @param string[] $values
     * @param string[] $predicate columns used to build the where-part of the query
     * @return mixed
     */
    public function update(string $table, array $values, array $predicate = [])
    {
        $updateParts = [];
        $paramValues = [];
        $i = 0;
        foreach ($values as $key => $value) {
            $col = $this->quoter->quoteColumn($key);
            $paramName = ':qp' . $i;
            $updateParts[] = $col . ' = ' . $paramName;
            $paramValues[$paramName] = $value;
            $i++;
        }

        $table = $this->quoter->quoteTable($table);
        $valueString = implode(',', $updateParts);
        $sql = "update {$table} set {$valueString} " . $this->buildPredicate($predicate, $paramValues);

        return $this->db->execute($sql, $paramValues);
    }

    /**
     * @param string $table
     * @param string[] $columns
     * @param string[] $predicate
     * @param string[] $paramValues
     * @return string
     */
    protected function buildSelectQuery(string $table, array $columns, array $predicate, array &$paramValues)
    {
        $cols = array_map(function ($col) {
            if ($col !== '*') {
                return $this->quoter->quoteColumn($col);
            }
            return $col;
        }, $columns);

        $table = $this->quoter->quoteTable($table);
        $colString = implode(',', $cols);
        return "select {$colString} from {$table} " . $this->buildPredicate($predicate, $paramValues);
    }

    /**
     * Select a single row from the database
     *
     * $helper->selectOne('cars', ['model'], ['make' => 'volvo']);
     *
     * @param string $table
     * @param string[] $columns
     * @param string[] $predicate
     * @return string[]
     */
    public function selectOne(string $table, array $columns = ['*'], array $predicate = [])
    {
        $paramValues = [];
        $sql = $this->buildSelectQuery($table, $columns, $predicate, $paramValues) . ' limit 1';
        return $this->db->query($sql, $paramValues);
    }

    /**
     * Select multiple rows from the database
     *
     * $helper->select('cars', ['model'], ['make' => 'volvo']);
     *
     * @param $table
     * @param string[] $columns
     * @param string[] $predicate
     * @return string[][]
     */
    public function select(string $table, array $columns = ['*'], array $predicate = [])
    {
        $paramValues = [];
        $sql = $this->buildSelectQuery($table, $columns, $predicate, $paramValues);
        return $this->db->queryAll($sql, $paramValues);
    }

    /**
     * Determine if a row exists
     *
     * $helper->exists('cars', ['make' => 'volvo']);
     *
     * @param string $table
     * @param string[] $predicate
     * @return bool
     */
    public function exists(string $table, array $predicate)
    {
        $paramValues = [];
        $table = $this->quoter->quoteTable($table);
        $sql = "select 1 from {$table} " . $this->buildPredicate($predicate, $paramValues) . ' limit 1';
        return !!$this->db->query($sql, $paramValues);
    }

    /**
     * @param string $table
     * @param string[] $values values to insert or update
     * @param string[] $predicate values to determine if the row exists
     * @return mixed
     */
    public function insertOrUpdate(string $table, array $values, array $predicate = [])
    {
        if ($this->exists($table, $predicate)) {
            return $this->update($table, $values, $predicate);
        }

        return $this->insert($table, $values);
    }

}
