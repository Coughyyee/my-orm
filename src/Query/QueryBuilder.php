<?php

namespace Szymo\MyOrm\Query;

use Exception;
use PDO;
use Szymo\MyOrm\Database\DB;

class QueryBuilder
{
    /**
     * @var string Table name.
     */
    protected string $table;

    /**
     * @var WhereClause[] Where conditions
     */
    protected array $wheres = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Appends to $wheres array the values of the parameters to build the conditional query.
     * @param string $col column name.
     * @param mixed $value value to check against.
     * @param string $operator [optional - default '='] custom operator for condition ex. '>', '<', '=' etc.
     * @return QueryBuilder (returns self) allows for function chaining - manditory.
     */
    public function where(string $col, mixed $value, string $operator = '='): self
    {
        $this->wheres[] = new WhereClause($col, $operator, $value);

        return $this;
    }

    /**
     * retrieves the first element from the condition. Must be appended after a conditional like where(...).
     * @throws Exception will throw if where clause not specified beforehand.
     * @return array<string, mixed>|null PDO::FETCH_ASSOC of the first row selected or null if none returned.
     */
    public function first(): array|null
    {
        // Prevent dangerous queries (no WHERE clause)
        if (empty($this->wheres)) {
            throw new Exception("Refusing to select without a WHERE clause.");
        }

        // Start base query
        $sql = "SELECT * FROM `{$this->table}`";

        // Build WHERE conditions
        $conditions = [];

        foreach ($this->wheres as $where) {
            $conditions[] = "`{$where->column}` {$where->operator} :{$where->column}";
        }

        // Append WHERE clause
        $sql .= " WHERE " . implode(" AND ", $conditions);

        // Limit to one result (MySQL syntax)
        $sql .= " LIMIT 1";

        // Prepare statement
        $stmt = DB::$db->prepare($sql);

        // Bind values safely
        foreach ($this->wheres as $where) {
            $stmt->bindValue(':' . $where->column, $where->value);
        }

        // Execute query
        $stmt->execute();

        // Fetch result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return null instead of false (cleaner API)
        return $result === false ? null : $result;
    }

    /**
     * Deletes row from table. Must be appended after a conditional like where(...).
     * @throws Exception will throw if where clause not specified beforehand.
     * @return void 
     */
    public function delete(): void
    {
        // Prevent dangerous full-table deletes
        if (empty($this->wheres)) {
            throw new Exception("Refusing to delete without a WHERE clause.");
        }

        // Start base query
        $sql = "DELETE FROM `{$this->table}`";

        // Build WHERE conditions
        $conditions = [];

        foreach ($this->wheres as $where) {
            $conditions[] = "`{$where->column}` {$where->operator} :{$where->column}";
        }

        // Append WHERE clause (safe because we already checked wheres)
        $sql .= " WHERE " . implode(" AND ", $conditions);

        // Prepare statement
        $stmt = DB::$db->prepare($sql);

        // Bind values
        foreach ($this->wheres as $where) {
            $stmt->bindValue(':' . $where->column, $where->value);
        }

        // Execute query
        $stmt->execute();
    }
}