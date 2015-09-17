<?php

class GoogleAnalyticsPopulateData
{
    /* -- Class properties -- */
    const DAYS = 30;

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
     * The dataManagers.
     *
     * @var array
     */
    private $dataManagers = null;

    /**
     * Main job handler.
     */
    public function fire($job, $data) {
        $this->user = User::find($data['user_id']);
        $this->collector = new GoogleAnalyticsDataCollector($this->user);
        foreach ($this->user->googleAnalyticsProperties()->get() as $property) {
            $this->property = $property;
            $this->dataManagers = $this->getManagers();
            $this->collectData();
        }
        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function collectData() {
        $data = $this->collectAllData();

        /* Getting metrics. */
        $sessionsData           = $data['sessions'];
        $bounceRateData         = $data['bounceRate'];
        $avgSessionDurationData = $data['avgSessionDuration'];

        /* Saving values. */
        $this->dataManagers['google_analytics_sessions']->saveData($sessionsData, TRUE);
        $this->dataManagers['google_analytics_bounce_rate']->saveData($bounceRateData, TRUE);
        $this->dataManagers['google_analytics_avg_session_duration']->saveData($avgSessionDurationData, TRUE);
        $this->dataManagers['google_analytics_top_sources']->initializeData();

        foreach ($this->dataManagers as $manager) {
            $manager->setWidgetsState('active');
        }
    }


    /**
     * Getting the property specific DataManagers
     * @return array
     */
    private function getManagers() {
        $dataManagers = array();

        foreach ($this->user->dataManagers()->get() as $generalDataManager) {
            $dataManager = $generalDataManager->getSpecific();

            if (($dataManager->descriptor->category == 'google_analytics') && ($dataManager->getCriteria()['property'] == $this->property->id)) {

                /* Setting dataManager. */
                $dataManagers[$dataManager->descriptor->type] = $dataManager;
            }
        }

        return $dataManagers;
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
            $start = Carbon::now()->subDays($i+1);
            $end = Carbon::now()->subDays($i);
            $metrics = $this->collector->getMetrics($this->property, $start->toDateString(), $end->toDateString(), array_keys($data));

            foreach ($metrics as $metric=>$dailyData) {
                /* Getting daily data. */
                $currentDateMetric = array();
                $currentDateMetric['timestamp'] = $end->getTimestamp();
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