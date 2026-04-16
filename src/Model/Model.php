<?php

namespace Szymo\MyOrm\Model;

use Exception;
use PDO;
use ReflectionObject;
use ReflectionProperty;
use Szymo\MyOrm\Database\DB;
use Szymo\MyOrm\Query\QueryBuilder;

class Model
{
    /**
     * @var int|null $id Each model will have an id column, the developer doesn not need to define it inside of their model class as it is defined here in the parent.
     */
    public ?int $id = null;

    /**
     * @var string $table Optional property - developer can define custom name for their table. If left empty the class name will be used as the table name for queries.
     */
    protected static string $table = '';


    /**
     * Inserts new row into the database table.
     * @throws Exception If a property is uninisialised (except if is defined as nullable).
     * @return void 
     */
    public function create()
    {
        // table becomes either the user defined table name or the model classes class name.
        $table = static::$table !== '' ? static::$table : strtolower(get_class($this));

        // Inspect the current object at runtime. 
        $reflection = new ReflectionObject($this);
        // get all public properites. 
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        // column names 
        $columns = [];
        // prepared statement placeholders for values 
        $placeholders = [];
        // values to bind 
        $values = [];

        foreach ($properties as $prop) {
            // skip static properties 
            if ($prop->isStatic()) {
                continue;
            }

            $name = $prop->getName();

            // check if property is not initialised. 
            if (!$prop->isInitialized($this)) {
                // skip nullable ('?') fields as they are allowed to be uninitialised. 
                if ($prop->getType()?->allowsNull()) {
                    continue;
                }

                // throw error as property hasnt been initialised. 
                throw new Exception("[$table] Property '$name' must be initialised before calling create() method.");
            }

            $value = $prop->getValue($this);

            // append values to arrays. 
            $columns[] = $name;
            $placeholders[] = ":$name";
            $values[$name] = $value;
        }

        // build sql query. 
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        // prepare sql query 
        $stmt = DB::$db->prepare($sql);

        // bind values to query 
        foreach ($values as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        // execute query -> can throw exception. 
        $stmt->execute();
    }

    /**
     * Creates a where condition. Has to be chained with another function.
     * @param string $col name of the column.
     * @param mixed $value the value for the condition to check.
     * @param string $operator [optional - default '='] custom operator for condition ex. '>', '<', '=' etc.
     * @return QueryBuilder<static> `Query builder is tied to calling Model.` Also allows for function chaining - manditory.
     */
    public static function where(string $col, mixed $value, string $operator = '='): QueryBuilder
    {
        // table becomes either the user defined table name or the model classes class name.
        $table = static::$table !== '' ? static::$table : strtolower(static::class);

        $builder = new QueryBuilder($table, static::class);
        return $builder->where($col, $value, $operator);
    }

    /**
     * Return all data from database table.
     * @return array<static>|null Returns an array of the model class objects or null if nothing.
     */
    public static function all(): array|null
    {
        // table becomes either the user defined table name or the model classes class name.
        $table = static::$table !== '' ? static::$table : strtolower(static::class);

        // sql query.
        $sql = "SELECT * FROM $table";

        // prepare and execute query.
        $stmt = DB::$db->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result === [])
            return null;

        // create model instance
        $modelClass = static::class;

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
     * Shorthand function for finding a row by its unique primary id.
     * @param int $id Unique identifier for row.
     * @return static|null Model object type or null if nothing found.
     */
    public static function find(int $id): object|null
    {
        // table becomes either the user defined table name or the model classes class name.
        $table = static::$table !== '' ? static::$table : strtolower(static::class);

        // sql query.
        $sql = "SELECT * FROM $table WHERE id = :id";

        // prepare and execute query.
        $stmt = DB::$db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false)
            return null;

        $modelClass = static::class;
        $model = new $modelClass();

        // loop over the result array and map values to the new model.
        foreach ($result as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = $value;
            }
        }

        return $model;
    }
}