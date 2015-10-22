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
                try {
                    $widget->saveSettings(array('type' => 'chart'));
                    Log::info('Changed type from ' . $oldType . ' to ' . 'chart');
                } catch (Exception $e) {
                    Log::warning('Could not change settings of widget #' .$widget->id . '.message:' . $e->getMessage());
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
