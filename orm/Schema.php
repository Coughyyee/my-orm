<?php

require_once 'Structure.php';

class Schema
{

    /**
     * Summary of create
     * @param string $tbl_name
     * @param callable $fn (Structure) : void
     * @return void
     */
    public static function create(string $tbl_name, callable $fn)
    {
        $structure = new Structure($tbl_name);

        // run the structure fn (defining table columns)
        $fn($structure);

        $columns_sql = [];

        // loop over the structures columns defined
        /**
         * @var ColumnDefinition $col
         */
        foreach ($structure->getColumns() as $col) {
            $sql = "`{$col->name}` {$col->type}";

            // append nullable
            if (!$col->nullable) {
                $sql .= " NOT NULL";
            }

            // append to array
            $columns_sql[] = $sql;
        }

        // create the query for creating the table
        $sql = "CREATE TABLE `$tbl_name` (" . implode(", ", $columns_sql) . ")";

        // execute query
        DB::$db->exec($sql);
    }

    public static function drop(string $table_name)
    {
        Structure::dropIfExists($table_name);
    }
}