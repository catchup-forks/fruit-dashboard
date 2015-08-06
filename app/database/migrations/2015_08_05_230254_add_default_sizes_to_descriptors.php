<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultSizesToDescriptors extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('widget_descriptors',function($table) {
            $table->tinyInteger('default_cols')->default(1);
            $table->tinyInteger('default_rows')->default(1);
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('widget_descriptors',function($table) {
            $table->dropColumn('default_cols');
            $table->dropColumn('default_rows');
        });
    }


}
