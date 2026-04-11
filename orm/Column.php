<?php

class Column {
    private array $column;

    public function __construct(array &$column) {
        $this->column = &$column;
    }

    public function nullable()
    {
        $this->column['nullable'] = 'true';
        return $this;
    }
}