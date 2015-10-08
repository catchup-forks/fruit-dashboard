<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameIdToPropertyIdOnGaProps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_analytics_properties', function($table) {
            $table->renameColumn('id', 'property_id');
        });
        Schema::table('google_analytics_properties', function($table) {
            $table->increments('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_analytics_properties', function($table) {
            $table->dropColumn('property_id');
        });
    }

}
