<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripeSubscriptionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('stripe_subscriptions',function($table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->integer('plan_id')->unsigned();
            $table->foreign('plan_id')
                  ->references('id')->on('stripe_plans');

            $table->string('customer', 127);
            $table->integer('quantity')->unsigned();
            $table->enum('status', array('active', 'inactive', 'trialing', 'past_due', 'canceled', 'unpaid'));
            $table->float('discount')->default(0);

            $table->dateTime('start');
            $table->dateTime('current_period_start');
            $table->dateTime('current_period_end');
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('trial_start');
            $table->dateTime('trial_end');

        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('stripe_subscriptions');
    }

}
