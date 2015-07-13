<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('widgets',function($table) {
            $table->increments('id');

            $table->integer('dashboard_id')->unsigned();
            $table->foreign('dashboard_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->integer('data_id')->unsigned()->nullable();
            $table->foreign('data_id')
                  ->references('id')->on('data');

            $table->string('name', 127);
            $table->longtext('description')->nullable();

            $table->string('position', 127);
            $table->string('settings', 255);

            $table->boolean('is_premium');

            $table->enum('type', array('clock', 'quote', 'greeting', 'financial'));
            $table->enum('state', array('active', 'setup_required'));
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('widgets');
    }


}
