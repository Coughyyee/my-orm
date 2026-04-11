<?php

require_once 'orm/Model.php';
require_once 'orm/DB.php';

new DB();

class User extends Model
{
}

$obj = new User();
$obj->name = 'hello value';
$obj->age = 10;
$obj->title = 'mr';
// $obj->create();

// // run once!
// require_once 'migrations/create_admin_table.php';
// $migration = new Create_admin_table();
// $migration->up();