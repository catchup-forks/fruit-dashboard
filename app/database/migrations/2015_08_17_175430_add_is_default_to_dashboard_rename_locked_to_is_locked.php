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

		// Make default dashboard for existing users
		foreach (User::all() as $user) {
			$dashboard = $user->dashboards->first();
			Log::info($dashboard);
			if ($dashboard != null) {
				$dashboard->is_default = TRUE;
				$dashboard->save();
			} else {
				/* Create new dashboard */
				$dashboard = new Dashboard(array(
				    'name'       => 'Personal dashboard',
				    'background' => 'On',
				    'number'     => 1,
				    'is_default' => TRUE
				));
				$dashboard->user()->associate($user);

				/* Save dashboard object */
				$dashboard->save();
			}
		}
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
