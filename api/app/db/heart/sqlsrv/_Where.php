<?php

namespace App\Db\Heart\Sqlsrv;

abstract class _Where extends _Database
{
    protected $where = [];
    protected $params = [];

    // Operadores SQL equivalentes para SQL Server
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
        'between' => 'BETWEEN',
        'notBetween' => 'NOT BETWEEN'
    ];

    /**
     * Method: addWhere
     * -----
     * Description:
     * Adds a WHERE clause segment with supported SQL Server operators.
     * 
     * @param string $column The column name to filter by.
     * @param string $operatorKey The logical operator (e.g., eq, like, between).
     * @param mixed $value The value(s) for the comparison.
     * @param string $boolean Logical connector (AND/OR).
     * -----
     */
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
        } else {
            $this->where[] = "$boolean $column $operator ?";
            $this->params[] = $value;
        }
    }

    /**
     * Method: buildWhere
     * -----
     * Description:
     * Builds the final WHERE clause string by joining all individual conditions.
     * 
     * @return string The assembled WHERE clause or empty string.
     * -----
     */
    protected function buildWhere()
    {
        if (empty($this->where)) {
            return '';
        }

        return 'WHERE ' . implode(' ', $this->where);
    }
}
