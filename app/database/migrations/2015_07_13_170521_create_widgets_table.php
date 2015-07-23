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
                  ->references('id')->on('dashboards')
                  ->onDelete('cascade');

            $table->integer('descriptor_id')->unsigned();
            $table->foreign('descriptor_id')
                  ->references('id')->on('widget_descriptors')
                  ->onDelete('cascade');

            $table->integer('data_id')->unsigned()->nullable();
            $table->foreign('data_id')
                  ->references('id')->on('data');

            $table->string('position', 127)->nullable();
            $table->string('settings', 255)->nullable();

            $table->enum('state', array('active', 'setup_required', 'hidden', 'missing_data'));
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
