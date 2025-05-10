<?php

use App\Db\Migration;

class AddEmailToUsersTable extends Migration
{
    public function up()
    {
        $this->raw("ALTER TABLE `users` ADD COLUMN `email` VARCHAR(255)");
    }

    public function down()
    {
        $this->raw("ALTER TABLE `users` DROP COLUMN `email`");
    }
}