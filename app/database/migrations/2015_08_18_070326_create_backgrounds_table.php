<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackgroundsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create backgrounds table
		Schema::create('backgrounds', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->boolean('is_enabled')->default(true);
            $table->integer('number')->nullable();
            $table->string('url')->nullable();
		});

		// Remove background_enabled from settings
		Schema::table('settings', function(Blueprint $table)
		{
			$table->dropColumn('background_enabled');
		});

		/* Create Background object to all existing users */
		foreach (User::all() as $user) {
			$bg = new Background;
			$bg->user()->associate($user);
			$bg->changeUrl();
			$bg->save();
		}

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Drop backgrounds
		Schema::dropIfExists('backgrounds');

		// Create background_enabled in settings
		Schema::table('settings', function(Blueprint $table)
		{
			$table->boolean('background_enabled')->default(true);
		});
	}

}