<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveVelocityToDashboard extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dashboards', function(Blueprint $table)
		{
            /* Add active velocity. */
			$table->string('active_velocity', 16)->default('days');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dashboards', function(Blueprint $table)
		{
            /* Delete active_velocity */ 
			$table->dropColumn('active_velocity');
		});
	}

}
