<?php

namespace Szymo\MyOrm\Query;

/**
 * Helper class for storing the where conditions for QueryBuilder
 */
class WhereClause
{
    /**
     * @param string $column Column name.
     * @param string $operator Operator to be used for conditional checking.
     * @param mixed $value Value to be checked for / against.
     */
    public function __construct(
        public string $column,
        public string $operator,
        public mixed $value
    ) {
    }
}