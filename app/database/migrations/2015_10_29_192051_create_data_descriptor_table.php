<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataDescriptorTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        /* Creating table. */
        Schema::create('data_descriptors', function($table) {
            $table->increments('id');
            $table->string('category', 127);
            $table->string('type', 127);
            $table->string('attributes', 255)->nullable();
        });

        /* Adding foreign key to data. */
        Schema::table('data', function($table) {
            $table->dropForeign('data_descriptor_id_foreign')->unsigned();
            /* Transformation required. */
            $table->foreign('descriptor_id')
                  ->references('id')->on('data_descriptors')
                  ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('data_descriptors');
    }
}
