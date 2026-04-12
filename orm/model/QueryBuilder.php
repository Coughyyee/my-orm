<?php

require_once 'WhereClause.php';

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
     * @return array<string, mixed>|false PDO::FETCH_ASSOC of the first row selected or false if none returned.
     */
    public function first(): array|false
    {
        $sql = "SELECT TOP 1 * FROM `{$this->table}`";

        // ensure that there is a where clause. 
        if (empty($this->wheres)) {
            throw new Exception("Refusing to select without a WHERE clause.");
        }

        $conditions = [];

        foreach ($this->wheres as $where) {
            $conditions[] = "`{$where->column}` {$where->operator} :{$where->column}";
        }

        // only add WHERE if conditions exist.
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = DB::$db->prepare($sql);

        // bind values to placeholders.
        foreach ($this->wheres as $where) {
            $stmt->bindValue(':' . $where->column, $where->value);
        }

        // execute query.
        $stmt->execute();

        // fetch and return
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Deletes row from table. Must be appended after a conditional like where(...).
     * @throws Exception will throw if where clause not specified beforehand.
     * @return void 
     */
    public function delete(): void
    {
        $sql = "DELETE FROM `{$this->table}`";

        // ensure that there is a where clause. Will result in deleting everything.
        // FUTURE: add a parameter that user can specify if they want to delete all or not (could be dangerous).
        if (empty($this->wheres)) {
            throw new Exception("Refusing to delete without a WHERE clause.");
        }

        $conditions = [];
        // loop over stored conditions.
        foreach ($this->wheres as $where) {
            // ex: `id` = 1
            $conditions[] = "`{$where->column}` {$where->operator} :{$where->column}";
        }

        // only add WHERE if conditions exist.
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = DB::$db->prepare($sql);

        // bind values to placeholders.
        foreach ($this->wheres as $where) {
            $stmt->bindValue(':' . $where->column, $where->value);
        }

        // execute query.
        $stmt->execute();
    }
}