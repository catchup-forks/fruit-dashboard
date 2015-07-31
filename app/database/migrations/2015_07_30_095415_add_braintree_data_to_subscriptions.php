<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBraintreeDataToSubscriptions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function(Blueprint $table)
        {
            $table->string('braintree_customer_id', 255)->nullable();
            $table->string('braintree_payment_method_token', 255)->nullable();
            $table->string('braintree_subscription_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function(Blueprint $table)
        {
            $table->dropColumn('braintree_customer_id');
            $table->dropColumn('braintree_payment_method_token');
            $table->dropColumn('braintree_subscription_id');
        });
    }

}


