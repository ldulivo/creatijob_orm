<?php

use App\Db\Migration;

class AddAuthFieldsToUsersTable extends Migration
{
    public function up()
    {
        $this->alter('users', function ($table) {
            $table->string('username', 100)->unique();
            $table->string('password', 255)->nullable();
            $table->string('role', 50)->default('admin');
            $table->string('status', 20)->default('active');
        });
    }

    public function down()
    {
        $this->dropColumn('users', 'status');
        $this->dropColumn('users', 'role');
        $this->dropColumn('users', 'password');
        $this->dropColumn('users', 'username');
    }
}

