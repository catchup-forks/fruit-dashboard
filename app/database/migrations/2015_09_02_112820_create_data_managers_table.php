<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataManagersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('data_managers',function($table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->integer('descriptor_id')->unsigned();
            $table->foreign('descriptor_id')
                  ->references('id')->on('widget_descriptors')
                  ->onDelete('cascade');

            $table->integer('data_id')->unsigned();
            $table->foreign('data_id')
                  ->references('id')->on('data')
                  ->onDelete('cascade');

            $table->string('settings_criteria', 255)->nullable();
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('data_managers');
    }

}
