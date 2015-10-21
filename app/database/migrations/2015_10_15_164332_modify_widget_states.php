<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyWidgetStates extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $deprecatedStates = array('hidden');

        /* Get current states. */
        $states = array();
        foreach (Widget::all() as $widget) {
            $states[$widget->id] = $widget->state;
        }
        Schema::table('widgets',function($table) {
            $table->dropColumn('state');
        });
        Schema::table('widgets',function($table) {
            $table->string('state', 16);
        });

        foreach ($states as $widgetId => $state) {
            $widget = Widget::find($widgetId);
            if (in_array($state, $deprecatedStates)) {
                $state = 'setup_required';
            }
            Log::info('Setting widget #' . $widgetId . ' state to ' . $state);
            $widget->setState($state);
        }
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }

}
