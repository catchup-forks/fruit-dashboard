<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePropertyToProfileOnGoogleWidgets extends Migration {

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
                if ( ! in_array('GoogleAnalyticsWidgetTrait', class_uses($widget))) {
                    /* Not a GA DM. */
                    continue;
                }
                /* Getting criteria */
                $criteria = $widget->getCriteria();

                if (array_key_exists('profile', $criteria) && $criteria['profile'] != '') {
                    /* Widget already set up. */
                    continue;
                }

                /* Creating collector instnace, and getting profile. */
                $collector = new GoogleAnalyticsDataCollector($widget->user());
                $property = $widget->getProperty();
                $profile = $collector->getProfiles($property)[0];

                /* Updating criteria. */
                $widget->saveSettings(array('profile' => $profile->id));
                Log::info("Added GA profile to widget #" . $widget->id);

            } catch (Exception $e) {
                Log::error('Error found while running migration: ' . get_class($this) . ' on Widget #' . $generalWidget->id . '. message: ' . $e->getMessage());
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
