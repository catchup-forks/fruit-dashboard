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
            $table->increments('id');
            $table->boolean('active')->default(false);

            $table->string('profile_id', 127);
            $table->integer('property_id')->unsigned();
            $table->foreign('property_id')
                  ->references('id')->on('google_analytics_properties')
                  ->onDelete('cascade');

            $table->string('name', 127);
         });
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
