<?php

require_once 'Column.php';
require_once 'ColumnDefinition.php';

class Structure
{
    protected string $table;
    protected array $columns = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id(): ColumnDefinition
    {
        $col = new ColumnDefinition('id', 'INT AUTO_INCREMENT PRIMARY KEY');
        $this->columns[] = $col;

        return $col;
    }

    public function string(string $name, int $length = 255): ColumnDefinition
    {
        $col = new ColumnDefinition($name, "VARCHAR($length)");
        $this->columns[] = $col;

        return $col;
    }

    public function integer($name): ColumnDefinition
    {
        $col = new ColumnDefinition($name, "INT");
        $this->columns[] = $col;

        return $col;
    }

    public function created_at(): ColumnDefinition
    {
        $col = new ColumnDefinition('created_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        $this->columns[] = $col;

        return $col;
    }

    public function updated_at(): ColumnDefinition
    {
        $col = new ColumnDefinition('updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        $this->columns[] = $col;

        return $col;
    }

    public static function dropIfExists(string $table)
    {
        DB::$db->exec("DROP TABLE IF EXISTS `$table`");
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getTable()
    {
        return $this->table;
    }
}