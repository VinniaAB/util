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
     * @param DatabaseInterface $db
     */
    function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * @param $col
     * @return string
     */
    protected function quoteColumn($col)
    {
        return "`{$col}`";
    }

    /**
     * @param string[] $predicate
     * @param string[] $paramValues inject parameter values into this array
     * @return string
     */
    protected function buildPredicate(array $predicate, array &$paramValues)
    {
        $wheres = [];
        $i = 0;
        foreach ($predicate as $key => $value) {
            $paramName = ':qq' . $i;
            $wheres[] = $this->quoteColumn($key) . ' = ' . $paramName;
            $paramValues[$paramName] = $value;
            $i++;
        }

        if (count($wheres) === 0) {
            return '';
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
    public function insert($table, array $values)
    {
        $cols = array_keys($values);
        $cols = array_map([$this, 'quoteColumn'], $cols);
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

        $table = $this->quoteColumn($table);
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
    public function update($table, array $values, array $predicate = [])
    {
        $updateParts = [];
        $paramValues = [];
        $i = 0;
        foreach ($values as $key => $value) {
            $col = $this->quoteColumn($key);
            $paramName = ':qp' . $i;
            $updateParts[] = $col . ' = ' . $paramName;
            $paramValues[$paramName] = $value;
            $i++;
        }

        $table = $this->quoteColumn($table);
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
    protected function buildSelectQuery($table, array $columns, array $predicate, array &$paramValues)
    {
        $cols = array_map(function ($col) {
            if ($col !== '*') {
                return $this->quoteColumn($col);
            }
            return $col;
        }, $columns);

        $table = $this->quoteColumn($table);
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
    public function selectOne($table, array $columns = ['*'], array $predicate = [])
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
    public function select($table, array $columns = ['*'], array $predicate = [])
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
    public function exists($table, array $predicate)
    {
        $paramValues = [];
        $table = $this->quoteColumn($table);
        $sql = "select 1 from {$table} " . $this->buildPredicate($predicate, $paramValues) . ' limit 1';
        return !!$this->db->query($sql, $paramValues);
    }

    /**
     * @param string $table
     * @param string[] $values values to insert or update
     * @param string[] $predicate values to determine if the row exists
     * @return mixed
     */
    public function insertOrUpdate($table, array $values, array $predicate = [])
    {
        if ($this->exists($table, $predicate)) {
            return $this->update($table, $values, $predicate);
        }

        return $this->insert($table, $values);
    }

}
