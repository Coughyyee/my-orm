<?php

class ColumnDefinition
{
    public string $name;
    public string $type;

    public bool $nullable = false;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }
}