<?php

use Szymo\MyOrm\Schema\Schema;
use Szymo\MyOrm\Schema\Structure;

class Create_admin_table
{
    public function up()
    {
        Schema::create('admins', function (Structure $table) {
            $table->id();
            $table->string('name');
            $table->integer('age')->nullable();
            $table->string('email')->unique();
            $table->created_at();
            $table->updated_at();
        });
    }

    public function down()
    {
        Schema::drop('admins');
    }
}