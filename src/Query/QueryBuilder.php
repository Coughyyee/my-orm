<?php

namespace Szymo\MyOrm\Query;

use Exception;
use PDO;
use Szymo\MyOrm\Database\DB;

/**
 * @template TModel Used for getting the model class that methods were called from.
 */
class QueryBuilder
{
    /**
     * @var WhereClause[] Where conditions
     */
    protected array $wheres = [];

    /**
     * QueryBuilder constructor method.
     * @param string $table Database table name.
     * @param class-string<TModel> $modelClass Class reference string.
     */
    public function __construct(protected string $table, protected string $modelClass)
    {
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
     * Returns all rows mapped to TModel type. Needs a where() before.
     * @throws Exception 
     * @return TModel[]|null array of mapped models or null if nothing found.
     */
    public function all(): array|null
    {
        // Prevent dangerous queries (no WHERE clause)
        if (empty($this->wheres)) {
            throw new Exception("Refusing to select without a WHERE clause.");
        }

        // Start base query
        $sql = "SELECT * FROM `{$this->table}`";

        // Build WHERE conditions
        $conditions = [];

        foreach ($this->wheres as $index => $where) {
            // ensure chaining same paramenters works
            $param = "{$where->column}_{$index}";

            $conditions[] = "`{$where->column}` {$where->operator} :{$param}";
        }

        // Append WHERE clause
        $sql .= " WHERE " . implode(" AND ", $conditions);

        // Prepare statement
        $stmt = DB::$db->prepare($sql);

        // Bind values safely
        foreach ($this->wheres as $index => $where) {
            // ensure chaining same paramenters works
            $param = "{$where->column}_{$index}";

            $stmt->bindValue(':' . $param, $where->value);
        }

        // Execute query
        $stmt->execute();

        // PDO result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result === [])
            return null;

        // create model instance
        $modelClass = $this->modelClass;

        /**
         * @var array<TModel>
         */
        $modelArray = [];

        foreach ($result as $index => $row) {
            $model = new $modelClass();

            foreach ($row as $key => $value) {
                if (property_exists($model, $key)) {
                    $model->$key = $value;
                }
            }

            $modelArray[] = $model;
        }

        return $modelArray;
    }

    /**
     * retrieves the first element from the condition. Must be appended after a conditional like where(...).
     * @param int $limit [optional - default '1'] The limit on how many results. Default is set to 1 - getting the first result.
     * @throws Exception 
     * @return TModel|TModel[]|null Returns the object of type whatever TModel is (whatever the Model was used to be called from) with the correct data filled in, an array of multiple TModels if a limit of more than 1 was specified or null if nothing was returned.
     */
    public function first(int $limit = 1): object|array|null
    {
        // Prevent dangerous queries (no WHERE clause)
        if (empty($this->wheres)) {
            throw new Exception("Refusing to select without a WHERE clause.");
        }

        // Ensure $limit is at least one.
        if ($limit < 1) {
            throw new Exception('$limit parameter must be at least 1.');
        }

        // Start base query
        $sql = "SELECT * FROM `{$this->table}`";

        // Build WHERE conditions
        $conditions = [];

        foreach ($this->wheres as $index => $where) {
            // ensure chaining same paramenters works
            $param = "{$where->column}_{$index}";

            $conditions[] = "`{$where->column}` {$where->operator} :{$param}";
        }

        // Append WHERE clause
        $sql .= " WHERE " . implode(" AND ", $conditions);

        // Limit to x amount of results (MySQL syntax)
        $sql .= " LIMIT $limit";

        // Prepare statement
        $stmt = DB::$db->prepare($sql);

        // Bind values safely
        foreach ($this->wheres as $index => $where) {
            // ensure chaining same paramenters works
            $param = "{$where->column}_{$index}";

            $stmt->bindValue(':' . $param, $where->value);
        }

        // Execute query
        $stmt->execute();

        // Fetch result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result === [])
            return null;

        // create model instance
        $modelClass = $this->modelClass;

        /**
         * @var array<TModel>
         */
        $modelArray = [];

        foreach ($result as $index => $row) {
            $model = new $modelClass();

            foreach ($row as $key => $value) {
                if (property_exists($model, $key)) {
                    $model->$key = $value;
                }
            }

            // if $limit is set to 1 aka only one result object should be returned.
            if ($limit === 1) {
                return $model;
            }

            // append model to models array.
            $modelArray[] = $model;
        }

        return $modelArray;
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

        foreach ($this->wheres as $index => $where) {
            // ensure chaining same paramenters works
            $param = "{$where->column}_{$index}";

            $conditions[] = "`{$where->column}` {$where->operator} :{$param}";
        }

        // Append WHERE clause (safe because we already checked wheres)
        $sql .= " WHERE " . implode(" AND ", $conditions);

        // Prepare statement
        $stmt = DB::$db->prepare($sql);

        // Bind values
        foreach ($this->wheres as $index => $where) {
            // ensure chaining same paramenters works
            $param = "{$where->column}_{$index}";

            $stmt->bindValue(':' . $param, $where->value);
        }

        // Execute query
        $stmt->execute();
    }
}