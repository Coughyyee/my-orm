# Migrations

In this document i will discuss everything to do with migrations inside of my ORM.

## Prerequisits

This migration system utilises the `DB` class found inside the Database directory. Here you can change the properties of the class for your connection to the MySQL database. The whole ORM utilises PHPs PDO for managing the connection and requests to the MySQL database.

## Creating a Migration

To create a migration, create a migration class which contains two methods: `public function up()` and `public function down()`. The `up()` method will be used for creating a migration to the database. Within this function you define the structure of your database table to be created using the `Schema::create()` class static method. Here is an example of a simple migration:

```php
class create_user_table {
    public function up()
    {
        Schema::create('users', function (Structure $table) {
            $table->id(); // Reccomended to always include an id column.
            $table->string('name'); // creates a column 'name' with 'varchar' as the type.
            $table->integer('age')->nullable(); // creates column 'age' with type of 'integer' and is also nullable.
            $table->string('email')->unique(); // creates a column 'email' with type of 'varchar' and is also got the UNIQUE attribute.
            $table->created_at(); // created at timestamp.
            $table->updated_at(); // updated at timestamp (automatically managed by mysql on updates).
        });
    }
}
```

The static function `create()` takes two parameters: a string name for the table name to be created and a callable function that has a `Structure` class as a parameter. Within the callable function you can define the structure of your database table by calling methods on the function parameter variable (**$table** in the example above) which define each column structure.

> If you have worked with Laravel before you might notice the similarity between the example above and Laravels own ORM. I have taken a lot of inspiration from how Laravels migrations function therefore i have made it similar as i believe its the best way to do migrations inside of PHP.

### Migration Table Methods

As you seen in the example above, i have utilised several methods to define the structure of the database table of *users*. Below is a list of all possible methods you can call to define the structure of a table:

| Method      | Description |
| ----------- | ----------- |
| id()      | Creates a column titled 'id' inside of your table. This column will have the properties of: **INT AUTO_INCREMENT PRIMARY KEY** |
| string()   | Creates a string column titled whatever was passed into the parameter. By default the type is `varchar(255)` but the length can be customised through the second optional parameter of the function. |
| integer()      | Created a integer column titled whatever was passed into the parameter. |
| created_at()      | Creates a column title 'created_at' with a **CURRENT_TIMESTAMP** as default value. |
| updated_at()      | Creates a column title 'updated_at' with a **CURRENT_TIMESTAMP** as default value aswell as an on update **CURRENT_TIMESTAMP**. Therefore MySQL manages the timestamp updates automatically. |

#### Column Manipulation Methods

You can also chain methods onto the main table structure methods to give the columns unique attributes. Here is a list of all possible chaining manipulation methods:

| Method      | Description |
| ----------- | ----------- |
| ->nullable()      | Allows the column to be **NULL**. |
| ->unique()      | Ensures that the values within the column are all unique. Adds **UNIQUE** constraint in MySQL. |

## Dropping Table Migration

As stated above, when defining the migration class you also include a `public function down()` method. This method houses the `Schema::drop()` function which calls the destruction of the table. The table name will be passed into te paramenter of the `drop()` function. When called the table will be **dropped**.
