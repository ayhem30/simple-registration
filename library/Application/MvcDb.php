<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

use Application\MvcApplication;
use Mysqli;

class MvcDb
{
    public static $MvcDb;
    private $db;
    private $query;
    private $where;
    private $group;
    private $order;
    private $limit;
    private $result;
    private $databaseConfiguration;

    public static function init()
    {
        if (!self::$MvcDb instanceof MvcDb) {
            if (MvcApplication::get()) {
                self::$MvcDb = new MvcDb(MvcApplication::get()->configuration['db']);
            } else {
                throw new \Exception('Cannot Retrieve MvcApplication instance.');
            }
        }
        return self::$MvcDb;
    }

    public function __construct($databaseConfiguration)
    {
        $this->setDbConfiguration($databaseConfiguration);
        $this->open();
    }

    public function setDbConfiguration($databaseConfiguration)
    {
        $this->databaseConfiguration = $databaseConfiguration;
    }

    public function open()
    {
        try {
            $this->db = new Mysqli(
                $this->databaseConfiguration['host'],
                $this->databaseConfiguration['username'],
                $this->databaseConfiguration['password'],
                $this->databaseConfiguration['schema']
            );

            return $this->db;
        } catch (\Exception $e) {
            throw $e;
        }

    }

    public function select($fields, $table)
    {
        if (!is_array($fields) || empty($table) || empty($fields)) {
            throw new \Exception('Please put the proper parameters.');
        }

        $tableColumns = implode(",",$fields);

        $this->query = "SELECT {$tableColumns} FROM {$table}";

        return $this;
    }

    public function insert($fields,$table,$values)
    {
        if (!is_array($fields) || empty($table) || !is_array($values)
            || count($fields) != count($values)) {
            throw new \Exception('Please put the proper parameters.');
        }

        $tableColumns = implode(",",$fields);

        $tableValues = "";
        foreach ($values as $value) {
            $tableValues .= "'" . $this->escape($value) . "',";
        }
        $tableValues = substr($tableValues,0,-1);

        $this->query = "INSERT INTO {$table}({$tableColumns}) VALUES({$tableValues})";

        return $this;
    }

    public function update($data,$table)
    {
        if (!is_array($data) || empty($table) || empty($data)) {
            throw new \Exception('Please put the proper parameters.');
        }

        $tableValues = "";
        foreach ($data as $key => $value) {
            $tableValues .= $this->escape($key) . " = '" . $this->escape($value) . "',";
        }
        $tableValues = substr($tableValues,0,-1);

        $this->query = "UPDATE {$table} SET {$tableValues}";

        return $this;
    }

    public function execute($query = '')
    {
        if (empty($this->query)) {
            throw new \Exception('Query is Empty.');
        }

        try {
            if (!empty($query)) {
                $this->result = mysqli_query($this->db,$query);
            }

            if (!empty($this->where)) {
                $this->query .= " WHERE {$this->where}";
            }

            if (!empty($this->group)) {
                $this->query .= " GROUP BY {$this->group}";
            }

            if (!empty($this->order)) {
                $this->query .= " ORDER BY {$this->order}";
            }

            if (!empty($this->limit)) {
                $this->query .= " LIMIT {$this->limit}";
            }

            $this->query .= ';';

            $this->result = mysqli_query($this->db,$this->query);

            return $this;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function fetchData()
    {
        $data = array();

        if (empty($this->result)) {
            throw new \Exception('Empty Query Result.');
        }

        while($rows = mysqli_fetch_assoc($this->result)) {
            $data[] = $rows;
        }

        return $data;
    }

    public function getLastInsertId()
    {
        return mysqli_insert_id($this->db);
    }

    public function getAffectedRows()
    {
        return mysqli_affected_rows($this->db);
    }

    public function getError(){
        return mysqli_error($this->db);
    }

    public function where($data,$separator = '')
    {
       if (!is_array($data) || empty($data)) {
           throw new \Exception('Data Parameter must be an array.');
       }

       foreach($data as $key => $value) {
            $this->where .= ((empty($separator)) ? ($key . " = '" . $this->escape($value) . "'") :
                            (strtoupper($separator) . ' ' . $key . " = '" . $this->escape($value) . "'"));
       }

       return $this;
    }

    public function buildConditions($type,$data)
    {
        if (!in_array(strtolower($type),array('order','group'))) {
            throw new \Exception('Type Parameter must be order/group only.');
        }

        if (!is_array($data) || empty($data)) {
            throw new \Exception('Data Parameter must be an array with values.');
        }

        $this->{$type} = implode(',',$data);

        return $this;
    }

    public function limit($start,$end)
    {
        if (!is_numeric($start) || !is_numeric($end)) {
            throw new \Exception('Parameters should be numeric.');
        }

        $this->limit = $start . ',' . $end;

        return $this;
    }


    public function escape($value)
    {
        return mysqli_real_escape_string($this->db,$value);
    }

    public function close()
    {
        mysqli_close($this->db);
    }

    public function __destruct()
    {
        $this->close();
    }
}
