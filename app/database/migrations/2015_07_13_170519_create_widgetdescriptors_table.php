<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetdescriptorsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('widget_descriptors',function($table) {
            $table->increments('id');

            $table->string('name', 127);
            $table->longtext('description')->nullable();

            $table->boolean('is_premium');

            $table->string('type', 127);
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('widget_descriptors');
    }



}
