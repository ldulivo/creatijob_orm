<?php

namespace App\Db\Heart\Mysql;

class _Main extends _QueryBuilder
{
  public function find($id)
  {
    $this->where('id', 'eq', $id);
    return $this;
  }

  public function all()
  {
    $sql = "SELECT {$this->columns} FROM {$this->table}";

    if (!empty($this->where)) {
      $sql .= ' WHERE ' . implode(' ', $this->where);
    }

    if (!empty($this->orderBy)) {
      $sql .= ' ORDER BY ' . $this->orderBy;
    }

    if (!empty($this->limit)) {
      $sql .= ' LIMIT ' . $this->limit;
    }

    if (!empty($this->offset)) {
      $sql .= ' OFFSET ' . $this->offset;
    }

    return self::query($sql, $this->params);
  }

  public function first()
  {
    $this->limit(1);
    $result = $this->all();
    return $result ? $result[0] : null;
  }

  public function last()
  {
    $this->orderBy('id', 'DESC');
    $this->limit(1);
    $result = $this->all();
    return $result ? $result[0] : null;
  }

  public function truncate()
  {
    $sql = "TRUNCATE TABLE {$this->table}";
    return self::execute($sql);
  }

  public function lastInsertId()
  {
    return self::getConnection()->lastInsertId();
  }

}