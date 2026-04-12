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
     * Inserts new row into the database table corrisponding with the name of the model subclass.
     * @throws Exception If a property is uninisialised (except if is defined as nullable).
     * @return void 
     */
    public function create()
    { // get string class name of model child class -> will be used as the database table name. 
        $class = get_class($this);
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
                throw new Exception("[$class] Property '$name' must be initialised before calling create() method.");
            }

            $value = $prop->getValue($this);

            // append values to arrays. 
            $columns[] = $name;
            $placeholders[] = ":$name";
            $values[$name] = $value;
        }

        // build sql query. 
        $sql = "INSERT INTO $class (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

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
     * @return QueryBuilder Allows for function chaining - manditory.
     */
    public static function where(string $col, mixed $value, string $operator = '='): QueryBuilder
    {
        // get string class name of model child class -> will be used as the database table name.
        $class = static::class;

        $builder = new QueryBuilder($class);
        return $builder->where($col, $value, $operator);
    }

    /**
     * Returns all rows from table corrisponding with the class name of model subclass.
     * @return array<int, array<string, mixed>> PDO::FETCH_ASSOC used to return all data. 
     */
    public static function all(): array
    {
        // get string class name of model child class -> will be used as the database table name.
        $class = static::class;

        // sql query.
        $sql = "SELECT * FROM $class";

        // prepare and execute query.
        $stmt = DB::$db->prepare($sql);
        $stmt->execute();

        // fetch all and return the array returned.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}