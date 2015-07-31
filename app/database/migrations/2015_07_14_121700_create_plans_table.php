<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('plans',function($table) {
            $table->increments('id');

            $table->string('name', 127);
            $table->longtext('description')->nullable();

            $table->enum('interval', array('day', 'week', 'month', 'year', 'permanent'));
            $table->integer('interval_count')->unsigned();
            $table->float('amount');
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('plans');
    }

}
