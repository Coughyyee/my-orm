# My PHP ORM (Side Project)

A simple ORM I am building to better understand how ORMs like Laravel's Eloquent work under the hood.

## 🧠 Why I built this

I've been using Laravel for a while and really enjoyed working with its ORM. I wanted to understand how it works internally, so I decided to build my own from scratch.

This project helped me learn more about:

* PHP OOP
* Advanced PHP concepts
* Database interaction with PDO
* Designing clean, chainable APIs

## Example Usage

### Model

```php
class User extends Model {}

$user = new User();
$user->name = "John";
$user->age = 25;
$user->create();
```

### Migration

```php
Schema::create('users', function (Structure $table) {
    $table->id();
    $table->string('name')->nullable();
    $table->integer('age');
});
```

## Notes

* This is a learning project, not production-ready yet...
* No query builder yet
* Limited validation and error handling

## Future Plans

* Query builder
* Relationships (hasMany, belongsTo)
* More schema features (indexes, defaults, etc.)
* Better structure and separation of concerns

