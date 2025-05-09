<?php

namespace App\Db\Heart\Mysql;

abstract class _Where extends _Database
{
  protected $where = [];
  protected $params = [];

  // Operadores SQL equivalentes
  protected $operators = [
      'eq' => '=',
      'ne' => '!=',
      'gt' => '>',
      'gte' => '>=',
      'lt' => '<',
      'lte' => '<=',
      'is' => 'IS',
      'not' => 'IS NOT',
      'in' => 'IN',
      'notIn' => 'NOT IN',
      'like' => 'LIKE',
      'notLike' => 'NOT LIKE',
      'iLike' => 'ILIKE', // PostgreSQL, opcional
      'notILike' => 'NOT ILIKE',
      'between' => 'BETWEEN',
      'notBetween' => 'NOT BETWEEN',
      'regexp' => 'REGEXP',
      'notRegexp' => 'NOT REGEXP',
      'iRegexp' => 'IREGEXP',
      'notIRegexp' => 'NOT IREGEXP',
      'any' => 'ANY', // PostgreSQL arrays, opcional
      'match' => 'MATCH' // fulltext search
  ];

  protected function addWhere($column, $operatorKey, $value, $boolean = 'AND')
  {
      $operator = $this->operators[$operatorKey] ?? '=';

      if (empty($this->where)) {
          $boolean = '';
      }

      if (in_array($operatorKey, ['in', 'notIn'])) {
          $placeholders = '(' . implode(',', array_fill(0, count($value), '?')) . ')';
          $this->where[] = "$boolean $column $operator $placeholders";
          foreach ($value as $v) {
              $this->params[] = $v;
          }
      } elseif (in_array($operatorKey, ['between', 'notBetween'])) {
          $this->where[] = "$boolean $column $operator ? AND ?";
          $this->params[] = $value[0];
          $this->params[] = $value[1];
      } elseif ($operatorKey === 'match') {
          $this->where[] = "$boolean MATCH($column) AGAINST (? IN BOOLEAN MODE)";
          $this->params[] = $value;
      } else {
          $this->where[] = "$boolean $column $operator ?";
          $this->params[] = $value;
      }
  }

  protected function buildWhere()
  {
      if (empty($this->where)) {
          return '';
      }
      
      return 'WHERE ' . implode(' ', $this->where);
  }
}
