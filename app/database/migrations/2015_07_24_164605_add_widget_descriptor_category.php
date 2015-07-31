<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWidgetDescriptorCategory extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('widget_descriptors',function($table) {
            $table->enum('category', array('personal', 'financial'));
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('widget_descriptors',function($table) {
            $table->dropColumn('category');
        });
    }




}
