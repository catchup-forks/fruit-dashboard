<?php

class GoogleAnalyticsAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'google_analytics_sessions' => '{"col":4,"row":1,"size_x":6,"size_y":6}',
        'google_analytics_bounce_rate' => '{"col":2,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'google_analytics';
    /* /LATE STATIC BINDING. */

    /**
     * The google analytics collector object.
     *
     * @var GoogleAnalyticsDataCollector
     */
    private $collector = null;

    /**
     * The google analytics property.
     *
     * @var GoogleAnalyticsProperty
     */
    private $property = null;

    /**
     * Setting up the collector.
     */
    protected function setup($args) {
        $this->collector = new GoogleAnalyticsDataCollector($this->user);
        $this->property = GoogleAnalyticsProperty::find($args['property_id']);
    }

    protected function createWidgets() {
        parent::createWidgets();
        foreach ($this->widgets as $widget) {
            $widget->setSetting('property', $this->property->id);
        }
    }

    protected function createManagers() {
        parent::createManagers();
        foreach ($this->dataManagers as $dataManager) {
            $dataManager->settings_criteria = json_encode(array(
                'property' => $this->property->id
            ));
            $dataManager->save();
        }
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        $sessionsDataManager   = $this->dataManagers['google_analytics_sessions'];
        $bounceRateDataManager = $this->dataManagers['google_analytics_bounce_rate'];
        $avgSessionDurationDataManager = $this->dataManagers['google_analytics_avg_session_duration'];

        $data = $this->collectAllData();

        /* Getting metrics. */
        $sessionsData           = $data['sessions'];
        $bounceRateData         = $data['bounceRate'];
        $avgSessionDurationData = $data['avgSessionDuration'];

        /* Saving values. */
        $sessionsDataManager->data->raw_value  = $this->createDbData($sessionsData);
        $bounceRateDataManager->data->raw_value = $this->createDbData($bounceRateData);
        $avgSessionDurationDataManager->data->raw_value = $this->createDbData($avgSessionDurationData);
    }

    /**
     * Creting the final DB-ready json
     *
     * @return string
     */
    private function createDbData($histogramData) {
        $dbData = array(
            'datasets' => array(),
            'data'     => array()
        );

        $i = 0;
        foreach ($histogramData as $entry) {
            /* Creating the new entry */
            $newEntry = array();
            foreach ($entry as $key=>$value) {
                if ($key == 'date') {
                    /* In date */
                    $newEntry['date'] = $value;
                } else {
                    /* In dataset */
                    if ( ! array_key_exists($key, $dbData['datasets'])) {
                        $dbData['datasets'][$key] = 'data_' . $i++;
                    }
                    $dataSetKey = $dbData['datasets'][$key];
                    $newEntry[$dataSetKey] = $value;
                }
            }
            array_push($dbData['data'], $newEntry);
        }

        return json_encode($dbData);
    }

    /**
     * Retrieving the full histogram from Google Analytics API
     *
     * @return array
     */
    private function collectAllData() {
        /* Initializing arrays. */
        $data = array(
            'sessions'           => array(),
            'bounceRate'         => array(),
            'avgSessionDuration' => array()
        );

        for ($i = self::DAYS; $i > 0; --$i) {
            /* Creating start, end days. */
            $start = Carbon::now()->subDays($i+1)->toDateString();
            $end = Carbon::now()->subDays($i)->toDateString();
            $metrics = $this->collector->getMetrics($this->property->id, $start, $end, array_keys($data));

            foreach ($metrics as $metric=>$dailyData) {
                /* Getting daily data. */
                $currentDateMetric = array();
                $currentDateMetric['date'] = $end;
                foreach ($dailyData as $profile=>$value) {
                    $currentDateMetric[$profile] = $value[0];
                }

                /* Adding value. */
                array_push($data[$metric], $currentDateMetric);
            }
        }
        return $data;
    }

}