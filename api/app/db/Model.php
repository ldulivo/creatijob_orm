<?php

namespace App\Db;

use Config;

abstract class Model
{
    protected $table;
    private $main;
    private $utils;

    public function __construct()
    {
        switch (Config\DB_DRIVER) {
            case 'mysql':
                $mainClass = \App\Db\Heart\Mysql\_Main::class;
                $utilsClass = \App\Db\Heart\Mysql\_Utils::class;
                break;

            case 'sqlsrv':
                $mainClass = \App\Db\Heart\Sqlsrv\_Main::class;
                $utilsClass = \App\Db\Heart\Sqlsrv\_Utils::class;
                break;

            default:
                throw new \Exception("Driver no soportado");
        }

        $this->main = new $mainClass();
        $this->utils = new $utilsClass();

        $this->main?->table($this->table);
        $this->main->select('*');
    }

    /*
    * Database
    */

    public function query($sql, $params = [])
    {
        return $this->main::query($sql, $params);
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->main::fetchAll($sql, $params);
    }

    public function fetch($sql, $params = [])
    {
        return $this->main::fetch($sql, $params);
    }

    public function insertQuery($sql, $params = [])
    {
        return $this->main::insertQuery($sql, $params);
    }

    public function execute($sql, $params = [])
    {
        return $this->main::execute($sql, $params);
    }

    /*
    * QueryBuilder
    */

    public function table($table)
    {
        return $this->main->table($table);
    }

    public function select($columns = '*')
    {
        return $this->main->select($columns);
    }

    public function where($column, $operatorKey, $value)
    {
        return $this->main->where($column, $operatorKey, $value);
    }

    public function orWhere($column, $operatorKey, $value)
    {
        return $this->main->orWhere($column, $operatorKey, $value);
    }

    public function orderBy($column, $direction = 'ASC')
    {
        return $this->main->orderBy($column, $direction);
    }

    public function limit($limit)
    {
        return $this->main->limit($limit);
    }

    public function offset($offset)
    {
        return $this->main->offset($offset);
    }

    public function get()
    {
        return $this->main->get();
    }

    public function count()
    {
        return $this->main->count();
    }

    public function insert($data)
    {
        return $this->main->insert($data);
    }

    public function update($data)
    {
        return $this->main->update($data);
    }

    public function delete()
    {
        return $this->main->delete();
    }

    /*
   * Main
   */
    public function find($id)
    {
        return $this->main->find($id);
    }

    public function all()
    {
        return $this->main->all();
    }

    public function first()
    {
        return $this->main->first();
    }

    public function last()
    {
        return $this->main->last();
    }

    public function truncate()
    {
        return $this->main->truncate();
    }

    public function lastInsertId()
    {
        return $this->main->lastInsertId();
    }


    /*
   * Utils
   */

    public function sanitize($data)
    {
        return $this->utils::sanitize($data);
    }

    public function checkConnection()
    {
        return $this->utils::checkConnection();
    }

    public function beginTransaction()
    {
        return $this->utils::beginTransaction();
    }

    public function commit()
    {
        return $this->utils::commit();
    }

    public function rollback()
    {
        return $this->utils::rollback();
    }

    public function exists($table, $column, $value)
    {
        return $this->utils::exists($table, $column, $value);
    }

    public function escape($value)
    {
        return $this->utils::escape($value);
    }

    public function prepareInsertData($data)
    {
        return $this->utils::prepareInsertData($data);
    }

    public function recordExists($table, $whereClause, $params)
    {
        return $this->utils::recordExists($table, $whereClause, $params);
    }
}
