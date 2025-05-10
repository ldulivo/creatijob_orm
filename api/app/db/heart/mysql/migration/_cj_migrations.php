<?php
namespace App\Db\Heart\Mysql\Migration;

use App\Db\Migration;

class _CreateCjMigrationsTable extends Migration
{
    public function up()
    {
        $this->create('cj_migrations', function ($table) {
            $table->id();
            $table->string('migration', 255);
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->drop('cj_migrations');
    }
}
