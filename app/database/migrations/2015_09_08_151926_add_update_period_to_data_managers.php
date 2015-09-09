<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatePeriodToDataManagers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('data_managers',function($table) {
            $table->integer('update_period')->default(6);
            $table->dateTime('last_updated');
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('data_managers',function($table) {
            $table->dropColumn('update_period');
            $table->dropColumn('last_updated');
        });
    }

}
