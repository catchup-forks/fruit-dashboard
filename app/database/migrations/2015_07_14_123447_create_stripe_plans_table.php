<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStripePlansTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('stripe_plans',function($table) {
            $table->increments('id');

            $table->string('plan_id', 127);
            $table->string('name', 127);

            $table->string('currency', 6);
            $table->float('amount');

            $table->string('interval', 16);
            $table->integer('interval_count')->unsigned();

            $table->boolean('livemode');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('stripe_plans');
    }


}
