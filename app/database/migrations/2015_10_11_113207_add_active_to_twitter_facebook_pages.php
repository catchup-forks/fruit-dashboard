<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveToTwitterFacebookPages extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('facebook_pages',function($table) {
            $table->boolean('active')->default(FALSE);
        });
        Schema::table('twitter_users',function($table) {
            $table->boolean('active')->default(FALSE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('facebook_pages',function($table) {
            $table->dropColumn('active');
        });
        Schema::table('twitter_users',function($table) {
            $table->dropColumn('active');
        });
    }

}
