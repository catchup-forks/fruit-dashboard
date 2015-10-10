<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOldDifferentiatedWidgets extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $deletableDescriptors = array(
            WidgetDescriptor::where('type', 'facebook_new_likes')->first(),
            WidgetDescriptor::where('type', 'twitter_new_followers')->first(),
        );
        foreach (Widget::all() as $widget) {
            try {
                $descriptor = null;
                if ( ! $widget instanceof HistogramWidget || ! in_array($widget->descriptor, $deletableDescriptors)) {
                    /* Not a histogram widget. */
                    continue;
                }
                if ( $widget instanceof TwitterNewFollowersWidget) {
                    $descriptor = WidgetDescriptor::where('type', 'twitter_followers')->first();
                    /* Finding data. */
                } else if ( $widget instanceof FacebookNewLikesWidget) {
                    $descriptor = WidgetDescriptor::where('type', 'facebook_likes')->first();
                }
                if (isset($descriptor)) {
                    if ( ! $widget->hasValidCriteria()) {
                        $widget->delete();
                        continue;
                    }
                    if (is_null($widget->dashboard)) {
                        $widget->delete();
                        continue;
                    }
                    $widget->descriptor()->associate($descriptor);
                    $widget->saveSettings(array('type' => 'diff'));
                    Log::info("Linked widget #" . $widget->id . " data to cumulative.");
                }

            } catch (Exception $e) {
                Log::error('Error found while running migration: ' . get_class($this) . ' on widget #' . $widget->id . '. message: ' . $e->getMessage());
            }
        }
        /* Deleting all dataManagers. */
        foreach (DataManager::all() as $dataManager) {
            try {
                if (in_array($dataManager->descriptor, $deletableDescriptors)) {
                    $dataManager->delete();
                }
            } catch (Exception $e) {
                Log::error('Error found while running migration: ' . get_class($this) . ' on datamanager #' . $generalDataManager->id . '. message: ' . $e->getMessage());
            }
        }
        /* Deleting descriptors. */
        foreach ($deletableDescriptors as $descriptor) {
            if ($descriptor) {
                $descriptor->delete();
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
