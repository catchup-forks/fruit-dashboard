<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleAnalyticsProfilesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('google_analytics_profiles',function($table) {
            $table->string('id', 127);

            $table->integer('property_id')->unsigned();
            $table->foreign('property_id')
                  ->references('id')->on('google_analytics_properties')
                  ->onDelete('cascade');

            $table->string('name', 127);
         });

        foreach (User::all() as $user) {
            if ($user->isServiceConnected('google_analytics')) {
                $gadc = new GoogleAnalyticsDataCollector($user);
                $gadc->saveProperties();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('google_analytics_profiles');
    }

}
