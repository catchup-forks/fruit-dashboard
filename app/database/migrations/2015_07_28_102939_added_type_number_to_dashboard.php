<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedTypeNumberToDashboard extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('dashboards',function($table) {
            $table->enum('type', array('personal', 'financial'));
            $table->tinyInteger('number');
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('dashboards',function($table) {
            $table->dropColumn('type');
            $table->dropColumn('number');
        });
    }

}
