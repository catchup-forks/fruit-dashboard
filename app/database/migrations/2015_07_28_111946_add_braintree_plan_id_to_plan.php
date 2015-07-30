<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBraintreePlanIdToPlan extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('plans',function($table) {
            $table->string('braintree_plan_id', 128)->nullable();
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('plans',function($table) {
            $table->dropColumn('braintree_plan_id');
        });
    }
}
