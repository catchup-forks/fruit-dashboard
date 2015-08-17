<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedLockedToDashboard extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('dashboards',function($table) {
            $table->boolean('locked');
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('dashboards',function($table) {
            $table->dropColumn('locked');
        });
    }

}
