<?php

require_once 'orm/model/Model.php';
require_once 'orm/DB.php';

new DB();

class User extends Model
{
    public string $name;
    public int $age;
    public string $title;
}

$obj = new User();
$obj->name = 'Emma';
$obj->age = 100;
$obj->title = 'mrs';
$obj->create();

// run once!
// require_once 'migrations/create_admin_table.php';
// $migration = new Create_admin_table();
// $migration->up();