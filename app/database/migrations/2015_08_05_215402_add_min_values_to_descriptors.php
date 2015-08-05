<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinValuesToDescriptors extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('widget_descriptors',function($table) {
            $table->tinyInteger('min_cols')->default(1);
            $table->tinyInteger('min_rows')->default(1);
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('widget_descriptors',function($table) {
            $table->dropColumn('min_cols');
            $table->dropColumn('min_rows');
        });
    }

}
