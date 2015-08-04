<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteUnnecessaryFieldsFromSubscription extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscriptions', function(Blueprint $table)
		{
			$table->dropColumn('current_period_start');
			$table->dropColumn('current_period_end');
			$table->dropColumn('canceled_at');
			$table->dropColumn('ended_at');
			$table->dropColumn('discount');
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
			$table->dateTime('current_period_start');
            $table->dateTime('current_period_end')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->float('discount')->default(0);
		});
	}

}
