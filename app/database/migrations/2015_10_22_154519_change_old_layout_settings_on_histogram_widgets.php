<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOldLayoutSettingsOnHistogramWidgets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        foreach (Widget::all() as $widget) {
            if ($widget instanceof HistogramWidget) {
                $oldType = $widget->getSettings()['type'];
                $widget->saveSettings(array('type' => 'chart'));
                Log::info('Changed type from ' . $oldType . ' to ' . 'chart');
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
