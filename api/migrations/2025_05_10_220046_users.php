<?php

use App\Db\Migration;

class Users extends Migration
{
    public function up()
    {
        $this->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->drop('users');
    }
}