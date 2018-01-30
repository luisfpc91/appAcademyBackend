<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbItemTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'db_item';

    /**
     * Run the migrations.
     * @table db_item
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('title')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->integer('id_image')->nullable()->default(null);
            $table->timestamp('created_at')->nullable()->default(null)->comment('				');
            $table->timestamp('updated_at')->nullable()->default(null);
            $table->timestamp('start_at')->nullable()->default(null);
            $table->integer('id_user')->nullable()->default(null);
            $table->integer('id_cat')->nullable()->default(null);
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
