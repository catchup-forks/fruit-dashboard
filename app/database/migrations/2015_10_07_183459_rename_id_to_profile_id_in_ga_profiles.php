<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameIdToProfileIdInGaProfiles extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('google_analytics_profiles', function($table) {
            $table->renameColumn('id', 'profile_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_analytics_profiles', function($table) {
            $table->renameColumn('profile_id', 'id');
        });
    }

}
