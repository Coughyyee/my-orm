<?php

require_once 'Structure.php';

class Schema
{

    /**
     * Creates a new table migration.
     * @param string $tbl_name name of the table to be created.
     * @param callable(Structure): void $fn Function that will be called to append all the column and column definitions for the new table.
     * @return void
     */
    public static function create(string $tbl_name, callable $fn): void
    {
        $structure = new Structure($tbl_name);

        // run the structure fn (defining table columns)
        $fn($structure);

        $columns_sql = [];

        // loop over the structures columns defined
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

    /**
     * Drop function to drop table.
     * @param string $table_name table name.
     * @return void
     */
    public static function drop(string $table_name): void
    {
        Structure::dropIfExists($table_name);
    }
}