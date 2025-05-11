<?php

use App\Db\Migration;

class AddCodeToUsersTable extends Migration
{
    public function up()
    {
        $this->alter('users', function ($table) {
            $table->string('code')->nullable();
        });
    }

    public function down()
    {
        $this->dropColumn('users', 'code');
    }
}