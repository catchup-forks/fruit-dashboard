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
        /* Cleaning up those goddamn WTF phantom dashboard widgets. */
        foreach (DB::table('widgets')->get(array('id', 'dashboard_id')) as $widget) {
            if (is_null(Dashboard::find($widget->dashboard_id))) {
                DB::table('widgets')->where('id', $widget->id)->delete();
            }
        }

        foreach (Widget::all() as $widget) {

            if (is_null($widget->dashboard)) {
                $widget->delete();
                continue;
            }

            if ( ! $widget->hasValidCriteria()) {
                continue;
            }
            /* Updating name attributes. */
            if ($widget instanceof HistogramWidget || $widget instanceof TableWidget) {
                $widget->saveSettings(array('name' => $widget->descriptor->name));   
            }

            /* Transforming countwidgets to histogram */
            if (in_array($widget->getDescriptor()->type, self::$countTypes)) {
                Log::info("Transforming count widget #" . $widget->id . " to histogram.");
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
                unset($settings['multiplier']);

                /* Commit changes. */
                $histogramWidget = Widget::find($widget->id);
                $histogramWidget->saveSettings(array_merge($newSettings, $settings));

            }
        }

        /* Deleting the count descriptors. */
        foreach (self::$countTypes as $type) {
            WidgetDescriptor::where('type', $type)->delete();
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
