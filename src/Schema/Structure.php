<?php

namespace Szymo\MyOrm\Schema;

use Szymo\MyOrm\Database\DB;


class Structure
{
    /**
     * @var string Table name
     */
    protected string $table;

    /**
     * Columns to be created.
     * @var array<string, ColumnDefinition> key = column name. value = ColumnDefiniton
     */
    protected array $columns = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Creates the id column for the table. Must be used when creating a query as it provides a PRIMARY KEY. 
     * By default the column will be called id and will be an AUTO_INCREMENT INT.
     * @return ColumnDefinition Allows for chaining functions.
     */
    public function id(): ColumnDefinition
    {
        $col = new ColumnDefinition('id', 'INT AUTO_INCREMENT PRIMARY KEY');
        $this->columns[] = $col;

        return $col;
    }

    /**
     * Creates a string column for the table.
     * @param string $name the name of the column.
     * @param int $length [optional - default '255'] varchar length
     * @return ColumnDefinition allows for chaining functions.
     */
    public function string(string $name, int $length = 255): ColumnDefinition
    {
        $col = new ColumnDefinition($name, "VARCHAR($length)");
        $this->columns[] = $col;

        return $col;
    }

    /**
     * Creates an integer column for the table.
     * @param string $name name of the column.
     * @return ColumnDefinition allows for chaining functions.
     */
    public function integer(string $name): ColumnDefinition
    {
        $col = new ColumnDefinition($name, "INT");
        $this->columns[] = $col;

        return $col;
    }

    /**
     * Timestamp. Creates a created_at column inside of the table.
     * Column will be called 'created_at'.
     * @return ColumnDefinition allows for chaining functions.
     */
    public function created_at(): ColumnDefinition
    {
        $col = new ColumnDefinition('created_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        $this->columns[] = $col;

        return $col;
    }

    /**
     * Timestamp. Creates a updated_at column inside of the table.
     * Column will be called 'updated_at'.
     * Automatically with update value of row due to ON UPDATE CURRENT_TIMESTAMP.
     * @return ColumnDefinition allows for chaining functions.
     */
    public function updated_at(): ColumnDefinition
    {
        $col = new ColumnDefinition('updated_at', "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        $this->columns[] = $col;

        return $col;
    }

    /**
     * Used for down migrations. Will drop the table specified.
     * @param string $table table name.
     * @return void
     */
    public static function dropIfExists(string $table)
    {
        DB::$db->exec("DROP TABLE IF EXISTS `$table`");
    }

    /**
     * Returns all columns from structure.
     * @return array<string, ColumnDefinition> key = column name. value = ColumnDefinition.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Returns the table name.
     * @return string table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }
}