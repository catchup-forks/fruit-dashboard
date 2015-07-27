<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBraintreeSubscriptionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('braintree_subscriptions',function($table) {
            $table->increments('id');

            $table->integer('plan_id')->unsigned();
            $table->foreign('plan_id')
                  ->references('id')->on('braintree_plans');

            $table->enum('status', array('active', 'inactive', 'trialing', 'past_due', 'canceled', 'unpaid'));

            $table->dateTime('start');

        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('braintree_subscriptions');
    }

}
