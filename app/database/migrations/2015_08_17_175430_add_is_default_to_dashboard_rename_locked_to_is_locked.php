<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDefaultToDashboardRenameLockedToIsLocked extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dashboards', function(Blueprint $table)
		{
			// Add is_default
			$table->boolean('is_default')->default(FALSE);
			
			// Rename locked to is_locked
			$table->dropColumn('locked');
			$table->boolean('is_locked')->default(FALSE);
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
			// Drop is_default
			$table->dropColumn('is_default');

			// Rename is_locked to locked
			$table->dropColumn('is_locked');
			$table->boolean('locked');
		});
	}

}
