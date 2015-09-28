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
            $table->dropColumn('id');
            $table->dropColumn('page_id');
        });
        Schema::table('facebook_pages',function($table) {
            $table->string('id', 127);
        });
        Schema::table('google_analytics_properties',function($table) {
            $table->dropColumn('id');
            $table->dropColumn('property_id');
        });
        Schema::table('google_analytics_properties',function($table) {
            $table->string('id', 127);
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
            $table->dropColumn('id');
        });
        Schema::table('facebook_pages',function($table) {
            $table->increments('id');
            $table->string('property_id', 127);
        });
        Schema::table('google_analytics_properties',function($table) {
            $table->dropColumn('id');
        });
        Schema::table('google_analytics_properties',function($table) {
            $table->increments('id');
            $table->string('property_id', 127);
        });
    }

}
