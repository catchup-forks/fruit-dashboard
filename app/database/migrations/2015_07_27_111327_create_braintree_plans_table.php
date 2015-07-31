<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBraintreePlansTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('braintree_plans',function($table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->string('plan_id', 127);
            $table->string('name', 127);

            $table->integer('billing_frequency');
            $table->float('price');

            $table->string('interval', 16);

            $table->string('currency', 6);
            $table->integer('billing_day')->unsigned()->nullable();

         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('braintree_plans');
    }

}
