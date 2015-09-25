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
        /* Creating TwitterUser connections to users */
        foreach (User::all() as $user) {
            if ( ! $user->isServiceConnected('twitter')) {
                continue;
            }
            try {
                $connector = new TwitterConnector($user);
                $connector->createTwitterUser();
                Log::info("Created profile for user #". $user->id);
            } catch (Exception $e) {
                Log::error('Error found while trying to a twitter user for user #' . $user->id . '. message: ' . $e->getMessage());

            }
        }

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
