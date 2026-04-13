# Models

In this document i will discuss everything to do with models inside of my ORM.

## Defining Model Classes

To define a model, you create a class and extend it with the Model class. This will turn your class into a model.

Within your model class you will need to define all the properties that reflect the columns of the database table. You can exclude id as it is automatically gathered through the Model parent class.

Properties must be public. This ensures the Model can access them correctly and allow for operations on the class properties. Any other type of modifier, ex. static or private, will be ignored by the Model class.

Properties can also be defined as nullable by prefixing the type of the property with a '?' symbol. This mean you're allowed to omit the inisialisation of the property and will be defined as null by default.

## Insertion of Data to Database

Your model reflects the structure of your database table you are utilising the model for. This means you should define all properties inside of the model class just like your columns are inside of your database. This means the correct amount and correct names.

The model class name is the same name that will be called to find the database table. This means if your model is defined as `User` then any operations will be done on the `user` table. This can be changed by defining an extra property inside your model class of: `protected static ?string $table = '...'` where you can define a custom table name which will be used for the queries instead.

To create a row and insert it into your table, first create the object and define its properties. For example a simple User model would look like this:

```php
class User extends Model {
    public string $name;
    public int $age;
    public string $gender;
}

$user = new User();
$user->name = 'Mike';
$user->age = 25;
$user->gender = 'male';
```

To then push this data into the database, you would write the following line of code:

```php
$user->create();
```

This function call will push the model into the database inside of the `user` table.

> If any columns within your database table are automatically managed by MySql, then you wont need to worry about defining them inside of your model. For example **id** could be **AUTO_INCREMENT**.

## Selecting All From Database Table

To select everything from a table, you would utilise the static `all()` function that is inside of the Model parent class. This will return an array of model objects or null if nothing. Model objects being the class the method was called from. For example, if we utilise the same User class as in the example above:

```php
$result = User::all(); // $result type is User[]|null.
```

## Fetching Specific Rows

To fetch all rows based on a specific condition you would utilise the `where(x, y)` static method on the model followed by the `all()` chained method. An example might be:

```php
// fetch all female users from user table
$female_users = User::where('gender', 'female')->all();
```

The result type of the **$female_users** variable will be of type `User[]|null` as the result can be either an array of all Users returned or null if result came back empty.

> The static `where()` method also includes an optional third parameter for the operator to be used within the query. By default it uses the '=' operator but you can explicitly tell it what operation it must do. Therefore for example: `User::where('age', 18, '>')->all()` will fetch all users with age of over 18.

## Fetching a Single Specific Row

To fetch a specific row from a table you would utilise the `where(x, y)` static method followed by the `first()` method. Upon doing this on your model, it will fetch from that models table the coresponding row based on the where condition, else it will return null. The returned value is a new object model with all the data inside of the correct properties of the model.

Here is an example with the User model from above:

```php
// Fetching a user with the id of 1.
$userResult = User::where('id', 1)->first();
```

**$userResult** obtains the type of `User|null` indicating either a `User` object or null. This means you are allowed to modify this object and retrieve data from it like a PHP class:

```php
$userResult->id; // valid - comes from the Model parent class property.
$userResult->name; // valid.
$userResult->age; // valid.
```

### Quick Shorthand

If you are looking for a single row with a specific id you can utilise the `find()` static method from your model. Calling this does the same thing as `where('id', 1)->first()`. It does look for the row column of **id** so if this row doesnt exist, this method will not work.

> TODO: Update the method to check for a custom id? Might have to create a property inside of the Model class to set a custom priamry id key column name.

### Custom Limit

Furthermore, you can pass an integer parameter into the `first()` function, specifying the amount of rows to be returned. This will now mean that if an integer greater than 1 has been passed into the function, the resulting type will be `TModel|null` (TModel specifying the dynamic type of the model class that was called from). Here is a similar example as the one above:

```php
// fetch the first 5 users with an age of 12.
$kids = User::where('age', '12')->first(5); // $kids now has the type of User[]|null.

// now you can loop over this and display their names.
foreach ($kids as $kid) {
    echo $kid->name; // prints out each users name.
}
```

## Deleting a Row

To delete a row from a table you would utilise the `where(x, y)` static method followed by the `delete()` method. When calling this from your model, it will delete a row from the table based on the condition supplied. Here is an example:

```php
// Delete user with id of 1.
User::where('id', 1)->delete();
```

> There must always be a where() before the delete().

TODO: Write this up
