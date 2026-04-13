<?php

// TEST PLAYGROUND

require __DIR__ . '/../vendor/autoload.php';

use Szymo\MyOrm\Database\DB;
use Szymo\MyOrm\Model\Model;

new DB();

class User extends Model
{
    protected static ?string $table = 'users'; // optional

    public string $name;
    public int $age;
    public string $title;
}

$result = User::where('id', 2, '>=')->first(2);
$r = User::find(1);

$result = User::all();

// echo '<pre>';
// var_dump($user);
// echo '</pre>';

// $result = User::all();
$result = User::where('name', 'a')->all();

echo '<pre>';
var_dump($result);
echo '</pre>';

// foreach ($result as $user) {
//     echo $user->id . '<br>';
// }


// run once!
// require_once '../migrations/create_admin_table.php';
// $migration = new Create_admin_table();
// $migration->up();