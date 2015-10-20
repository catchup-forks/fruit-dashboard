<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserSettingsRefactor extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add existing attributes to settings
        Schema::table('settings', function(Blueprint $table)
        {
            $table->timestamp('last_activity')->nullable();
            $table->string('api_key', 255)->nullable();
            $table->string('startup_type', 255)->nullable();
        });

        // Move values to settings
        foreach (User::all() as $user) {
            $user->settings->last_activity = $user->last_activity;
            $user->settings->api_key       = $user->api_key;
            $user->settings->startup_type  = $user->startup_type;
            $user->settings->save();
        }

        // Remove attributes from user
        Schema::table('users', function(Blueprint $table)
        {
            $table->dropColumn('last_activity');
            $table->dropColumn('api_key');
            $table->dropColumn('startup_type');
        });

        // Add new (and remove unnecessary) attributes to settings
        Schema::table('settings', function(Blueprint $table)
        {
            $table->dropColumn('newsletter_frequency');
            $table->string('onboarding_state', 255)->nullable();
            $table->string('project_name', 255)->nullable();
            $table->string('project_url', 255)->nullable();
            $table->string('company_size', 255)->nullable();
            $table->string('company_funding', 255)->nullable();
        });

        // Set new attributes for existing users
        foreach (User::all() as $user) {
            $user->settings->onboarding_state = 'finished';
            $user->settings->project_name = null;
            $user->settings->project_url = null;
            $user->settings->company_size = null;
            $user->settings->company_funding = null;
            $user->settings->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove attributes from settings
        Schema::table('settings', function(Blueprint $table)
        {
            $table->integer('newsletter_frequency');
            $table->dropColumn('onboarding_state');
            $table->dropColumn('project_name');
            $table->dropColumn('project_url');
            $table->dropColumn('company_size');
            $table->dropColumn('company_funding');
        });

        // Add attributes to users
        Schema::table('users', function(Blueprint $table)
        {
            $table->timestamp('last_activity')->nullable();
            $table->string('api_key', 255)->nullable();
            $table->string('startup_type', 255);
        });

        // Move values to users
        foreach (User::all() as $user) {
            $user->last_activity = $user->settings->last_activity;
            $user->api_key       = $user->settings->api_key;
            $user->startup_type  = $user->settings->startup_type ;
            $user->save();
        }

        // Remove attributes from settings
        Schema::table('settings', function(Blueprint $table)
        {
            $table->dropColumn('last_activity');
            $table->dropColumn('api_key');
            $table->dropColumn('startup_type');
        });
    }

}
