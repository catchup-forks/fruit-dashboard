<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePlanColumns extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function(Blueprint $table)
        {
            $table->dropColumn('interval');
            $table->dropColumn('interval_count');
            $table->string('braintree_merchant_account_id', 128)->nullable();
            $table->string('braintree_merchant_currency', 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function(Blueprint $table)
        {
            $table->enum('interval', array('day', 'week', 'month', 'year', 'permanent'));
            $table->integer('interval_count')->unsigned();
            $table->dropColumn('braintree_merchant_account_id');
            $table->dropColumn('braintree_merchant_currency');
        });
    }

}
