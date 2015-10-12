<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStateToDataManagers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('data_managers',function($table) {
            $table->enum('state', array('active', 'loading'))->default('active');
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('data_managers',function($table) {
            $table->dropColumn('state');
        });
    }

}
