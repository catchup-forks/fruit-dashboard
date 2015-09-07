<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovedUniqueFromServiceIds extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('facebook_pages',function($table) {
            $table->dropUnique('facebook_pages_id_unique');
        });
        Schema::table('google_analytics_properties',function($table) {
            $table->dropUnique('google_analytics_properties_id_unique');
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facebook_pages',function($table) {
            $table->string('id', 127)->unique();
        });
        Schema::table('google_analytics_properties',function($table) {
            $table->string('id', 127)->unique();
        });
    }

}
