<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalColumnToEvents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('events', function($table)
        {
             // adding title
            $table->longText('original')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasColumn('users','original'))
		{	
			Schema::table('users', function($table)
	        {
	            // dropping column
	            $table->dropColumn('original');
	        });
		}
	}
}
