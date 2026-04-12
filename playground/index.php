<?php

require __DIR__ . '/../vendor/autoload.php';

use Szymo\MyOrm\Database\DB;
use Szymo\MyOrm\Model\Model;

new DB();

class User extends Model
{
    public string $name;
    public int $age;
    public string $title;
}

// creating user
$obj = new User();
$obj->name = 'Emma';
$obj->age = 100;
$obj->title = 'mrs';
$obj->create();

// fetching users
$all = User::all();
// fetching specific user
$specific = User::where('id', 1)->first();
// deleting user
// User::where('id', 1)->delete();

echo '<pre>';
// var_dump($all);
echo '</pre>';

echo '<pre>';
var_dump($specific);
echo '</pre>';

// run once!
// require_once 'migrations/create_admin_table.php';
// $migration = new Create_admin_table();
// $migration->up();