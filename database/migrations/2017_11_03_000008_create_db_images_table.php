<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbImagesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'db_images';

    /**
     * Run the migrations.
     * @table db_images
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('id_user')->nullable()->default(null);
            $table->text('path')->nullable()->default(null);
            $table->text('name')->nullable()->default(null);
            $table->string('id_categories', 45)->nullable()->default(null);
            $table->integer('search_index')->nullable()->default(null);
            $table->text('youtube_link')->nullable()->default(null);
            $table->enum('type', ['img', 'video'])->nullable()->default('img');
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
