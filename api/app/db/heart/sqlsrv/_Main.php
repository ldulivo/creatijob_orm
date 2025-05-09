<?php

namespace App\Db\Heart\Sqlsrv;

class _Main extends _QueryBuilder
{
    public function find($id)
    {
        $this->where('id', 'eq', $id);
        return $this;
    }

    /**
     * Method: all
     * -----
     * Description:
     * Executes a SELECT query to retrieve all matching records.
     * Builds the query manually and applies OFFSET/FETCH for SQL Server compatibility.
     * 
     * @return array The result set as an array of rows.
     * -----
     */
    public function all()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . $this->orderBy;
        } elseif (!empty($this->limit) || !empty($this->offset)) {
            $sql .= ' ORDER BY (SELECT NULL)';
        }

        if ($this->offset !== '' || $this->limit !== '') {
            $offset = $this->offset !== '' ? (int) $this->offset : 0;
            $limit = $this->limit !== '' ? (int) $this->limit : 10;
            $sql .= " OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
        }

        return self::fetchAll($sql, $this->params);
    }

    /**
     * Method: first
     * -----
     * Description:
     * Retrieves the first record from the query result.
     * Internally applies a LIMIT of 1 using SQL Server's OFFSET/FETCH strategy.
     * 
     * @return array|null The first matching record or null if none found.
     * -----
     */
    public function first()
    {
        $this->limit(1);
        $result = $this->all();
        return $result ? $result[0] : null;
    }

    /**
     * Method: last
     * -----
     * Description:
     * Retrieves the last record based on descending order of 'id'.
     * Uses LIMIT 1 (via OFFSET/FETCH) for SQL Server compatibility.
     * 
     * @return array|null The last matching record or null if none found.
     * -----
     */
    public function last()
    {
        $this->orderBy('id', 'DESC');
        $this->limit(1);
        $result = $this->all();
        return $result ? $result[0] : null;
    }

    /**
     * Method: truncate
     * -----
     * Description:
     * Executes a TRUNCATE TABLE statement on the current table.
     * 
     * @return int The number of affected rows (usually 0).
     * -----
     */
    public function truncate()
    {
        $sql = "TRUNCATE TABLE {$this->table}";
        return self::execute($sql);
    }

    /**
     * Method: lastInsertId
     * -----
     * Description:
     * Retrieves the last inserted ID using PDO for SQL Server.
     * 
     * @return string The last inserted ID.
     * -----
     */
    public function lastInsertId()
    {
        return self::getConnection()->lastInsertId();
    }
}
