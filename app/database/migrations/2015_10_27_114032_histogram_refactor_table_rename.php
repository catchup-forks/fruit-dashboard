<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HistogramRefactorTableRename extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
    {
        foreach (Widget::all() as $widget) {
            if ( ! $widget->hasValidCriteria()) {
                continue;
            }
            /* Updating name attributes. */
            if ($widget instanceof HistogramWidget || $widget instanceof TableWidget) {
                $widget->saveSettings(array('name' => $widget->descriptor->name));   
            }

            /* Transforming countwidgets to histogram */
            if ($widget instanceof CountWidget) {
                $settings = $widget->getSettings();
                $newDescriptor = WidgetDescriptor::where('type', $widget::$histogramDescriptor)
                    ->first();

                $newSettings = array(
                    'resolution' => $settings['period'],
                    'length'     => $settings['multiplier'],
                    'name'       => $newDescriptor->name,
                    'type'       => 'count'
                );

                /* Removing unnecessary attributes, */
                unset($settings['period']);
                unset($settings['resolution']);

                /* Reassigning descriptor. */
                $widget->descriptor_id = $newDescriptor->id;
                $widget->save();

                /* Adding settings. */
                $histogramWidget = Widget::find($widget->id);
                $histogramWidget->saveSettings(array_merge($newSettings, $settings));

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
