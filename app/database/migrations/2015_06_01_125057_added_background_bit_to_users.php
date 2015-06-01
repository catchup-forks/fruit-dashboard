<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedBackgroundBitToUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table)
        {
             // adding title
            $table->boolean('isBackgroundOn')->default(true);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasColumn('users','isBackgroundOn'))
        {
            Schema::table('users', function($table)
            {
                // dropping column
                $table->dropColumn('isBackgroundOn');
            });
        }
	}
}
