<?php

require __DIR__ . '/../vendor/autoload.php';

use Szymo\MyOrm\Database\DB;
use Szymo\MyOrm\Model\Model;

new DB();

class User extends Model
{
    // TODO: add a custom field that allows developers to specify a different table name so it doesnt always have to match the class name? Something like: protected string $table = 'user_table';
    public string $name;
    public int $age;
    public string $title;
}

// $user = User::where('id', 1)->first();

// echo '<pre>';
// var_dump($user);
// echo '</pre>';

$result = User::all();

echo '<pre>';
var_dump($result);
echo '</pre>';


// run once!
// require_once '../migrations/create_admin_table.php';
// $migration = new Create_admin_table();
// $migration->up();