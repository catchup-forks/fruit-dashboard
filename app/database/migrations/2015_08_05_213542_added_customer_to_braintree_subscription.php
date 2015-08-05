<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedCustomerToBraintreeSubscription extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('braintree_subscriptions',function($table) {
            $table->string('customer_id', 64);
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('braintree_subscriptions',function($table) {
            $table->dropColumn('customer_id');
        });
    }
}
