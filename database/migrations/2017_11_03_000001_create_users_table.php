<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->rememberToken();
            $table->string('last_name', 45)->nullable()->default(null);
            $table->string('bod', 45)->nullable()->default(null);
            $table->string('phone', 15)->nullable()->default(null);
            $table->string('address', 45)->nullable()->default(null);
            $table->string('uuid', 45)->nullable()->default(null);
            $table->text('avatar')->nullable()->default(null);
            $table->enum('level', ['user', 'manager', 'admin'])->nullable()->default('user');

            $table->unique(["email"], 'users_email_unique');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->set_schema_table);
     }
}
