<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddNameSettingToHistogramWidgets extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Widget::all() as $generalWidget) {
            try {
                $widget = $generalWidget->getSpecific();
                if ( ! $widget instanceof HistogramWidget) {
                    continue;
                }
                if ($widget instanceof iServiceWidget) {
                    $widget->saveSettings(array('name' => $widget->getDefaultName()));
                }
            } catch (Exception $e) {
                Log::error('Error found while running migration: ' . get_class($this) . ' on widget #' . $widget->id . '. message: ' . $e->getMessage());
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
        //
    }

}
