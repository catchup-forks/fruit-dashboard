<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleAnalyticsGoalsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_analytics_goals',function($table) {
            $table->increments('id');
            $table->boolean('active')->default(false);

            $table->string('goal_id', 127);

            $table->integer('profile_id')->unsigned();
            $table->foreign('profile_id')
                  ->references('id')->on('google_analytics_profiles')
                  ->onDelete('cascade');

            $table->string('name', 127);
         });

        Artisan::call('google_analytics:refresh_properties');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_analytics_goals');
    }

}
