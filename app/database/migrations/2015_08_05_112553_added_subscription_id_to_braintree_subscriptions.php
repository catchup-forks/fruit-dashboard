<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedSubscriptionIdToBraintreeSubscriptions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('braintree_subscriptions',function($table) {
            $table->string('subscription_id', 32);
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('braintree_subscriptions',function($table) {
            $table->dropColumn('subscription_id');
        });
    }

}
