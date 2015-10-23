<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssignDataToCountwidgets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        foreach (Widget::all() as $widget) {
            if ($widget instanceof CountWidget) {
                try {
                    $widget->assignData();
                    $widget->save();
                    Log::info('Data assigned to widget #' . $widget->id);
                } catch (Exception $e) {
                    Log::error('Could not assign data to widget #' . $widget->id);
                }
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
	}

}
