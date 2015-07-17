<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('subscriptions',function($table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->integer('plan_id')->unsigned();
            $table->foreign('plan_id')
                  ->references('id')->on('plans');

            $table->dateTime('current_period_start');
            $table->dateTime('current_period_end');
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('ended_at')->nullable();

            $table->enum('status', array('active', 'ended', 'canceled'));
            $table->float('discount')->default(0);

            $table->timestamps();
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('subscriptions');
    }

}
