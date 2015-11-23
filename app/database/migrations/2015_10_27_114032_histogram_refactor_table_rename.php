<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class HistogramRefactorTableRename extends Migration
{
    private static $countTypes = array(
        'twitter_followers_count',
        'facebook_likes_count',
        'google_analytics_sessions_count'
    );
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
            if (in_array($widget->getDescriptor()->type, self::$countTypes)) {
                $settings = $widget->getSettings();
                $newDescriptor = WidgetDescriptor::where('type', $widget::$histogramDescriptor)
                    ->first();

                /* Reassigning descriptor. */
                $widget->descriptor_id = $newDescriptor->id;
                $widget->save();

                /* Adding settings. */
                $newSettings = array(
                    'resolution' => $settings['period'],
                    'length'     => $settings['multiplier'],
                    'type'       => SiteConstants::LAYOUT_COUNT
                );

                /* Removing unnecessary attributes, */
                unset($settings['period']);
                unset($settings['resolution']);

                /* Commit changes. */
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
