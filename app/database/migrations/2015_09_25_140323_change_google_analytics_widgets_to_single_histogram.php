<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGoogleAnalyticsWidgetsToSingleHistogram extends Migration {

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
                if ( ! in_array('GoogleAnalyticsDataManagerTrait', class_uses($dataManager)) || ! $dataManager instanceof HistogramDataManager) {
                    continue;
                }

                $data = json_decode($dataManager->data->raw_value, 1);
                if ( ! array_key_exists('datasets', $data)) {
                    continue;
                }

                $firstDataSet = array_values($data['datasets'])[0];

                /* Dealing with a GA MHDM. */
                $newData = array();
                foreach ($data['data'] as $entry) {
                    $newEntry = array(
                        'timestamp' => $entry['timestamp'],
                        'value'     => $entry[$firstDataSet]
                    );
                    array_push($newData, $newEntry);
                }

                $dataManager->data->raw_value = json_encode($newData);
                $dataManager->data->save();

            } catch (Exception $e) {
                Log::error('Error found while running migration: ' . get_class($this) . ' on DataManager #' . $dataManager->id . '. message: ' . $e->getMessage());
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
