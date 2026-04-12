<?php

namespace Szymo\MyOrm\Schema;

class ColumnDefinition
{
    /**
     * @var string Name of the column.
     */
    public string $name;

    /**
     * @var string Type and attributes of the column.
     */
    public string $type;

    /**
     * @var bool [optional - default 'false'] nullable attribute.
     */
    public bool $nullable = false;

    /**
     * @var bool [optional - default 'false'] unique attribute.
     */
    public bool $unique = false;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Makes the current column nullable.
     * @return ColumnDefinition allows for chaining.
     */
    public function nullable(): self
    {
        $this->nullable = true;
        return $this;
    }

    /**
     * Makes the current column include a unique constriant.
     * @return ColumnDefinition allows for chaining.
     */
    public function unique(): self
    {
        $this->unique = true;
        return $this;
    }
}