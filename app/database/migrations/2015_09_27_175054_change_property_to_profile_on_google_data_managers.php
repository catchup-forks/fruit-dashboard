<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePropertyToProfileOnGoogleDataManagers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (DataManager::all() as $generalDataManager) {
            try {
                $dataManager = $generalDataManager->getSpecific();
                if ( ! in_array('GoogleAnalyticsDataManagerTrait', class_uses($dataManager))) {
                    /* Not a GA DM. */
                    continue;
                }
                /* Getting criteria */
                $criteria = $dataManager->getCriteria();

                if (array_key_exists('profile', $criteria) && $criteria['profile'] != '') {
                    /* Widget already set up. */
                    continue;
                }


                /* Creating collector instnace, and getting profile. */
                $collector = new GoogleAnalyticsDataCollector($dataManager->user);
                $property = $dataManager->getProperty();
                $profile = $collector->getProfiles($property)[0];

                /* Updating criteria. */
                $criteria['profile'] = $profile->id;
                $dataManager->settings_criteria = json_encode($criteria);

                /* Updating widget settings. */
                foreach ($dataManager->widgets() as $generalWidget) {
                    $widget = $generalWidget->getSpecific();
                    $widget->saveSettings(array('profile' => $profile->id));
                }

                $dataManager->save();
                Log::info("Added GA profile to Datamanager #" . $dataManager->id);

            } catch (Exception $e) {
                Log::error('Error found while running migration: ' . get_class($this) . ' on DataManager #' . $generalDataManager->id . '. message: ' . $e->getMessage());
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
