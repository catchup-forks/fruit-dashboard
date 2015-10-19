<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutodashboardStateToWidgetSharings extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('widget_sharings',function($table) {
            $table->dropColumn('state');
        });
        Schema::table('widget_sharings',function($table) {
            $table->enum('state', array(
                'not_seen',
                'seen',
                'accepted',
                'rejected',
                'auto_created'
            ));
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }

}
