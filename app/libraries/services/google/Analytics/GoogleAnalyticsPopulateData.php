<?php

class GoogleAnalyticsPopulateData
{
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
     * The google analytics property.
     *
     * @var string
     */
    private $profileId = null;

    /**
     * The dataManagers.
     *
     * @var array
     */
    private $dataManagers = null;

    /**
     * The dataManager criteria.
     *
     * @var array
     */
    private $criteria = null;

    /**
     * Main job handler.
     */
    public function fire($job, $data) {
        $this->user = User::find($data['user_id']);
        $time = microtime(TRUE);
        Log::info("Starting Google Analytics data collection for user #". $this->user->id . " at " . Carbon::now()->toDateTimeString());
        $this->collector = new GoogleAnalyticsDataCollector($this->user);
        $this->criteria = $data['criteria'];
        $this->property = $this->user->googleAnalyticsProperties()->where('id', $this->criteria['property'])->first();
        $this->profileId = $this->criteria['profile'];
        $this->dataManagers = $this->getManagers();
        $this->populateData();
        Log::info("Google Analytics data collection finished and it took " . (microtime($time) - $time) . " seconds to run.");
        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
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

            if ($dataManager->descriptor->category == 'google_analytics' && $dataManager->getCriteria() == $this->criteria) {
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

        for ($i = SiteConstants::getServicePopulationPeriod()['google_analytics']; $i >= 0; --$i) {
            /* Creating start, end days. */
            $start = SiteConstants::getGoogleAnalyticsLaunchDate();
            $end = Carbon::now()->subDays($i);
            $metrics = $this->collector->getMetrics($this->property, $this->profileId, $start, $end->toDateString(), array_keys($data));

            foreach ($metrics as $metric=>$value) {
                /* Getting daily data. */
                $currentDateMetric = array();
                $currentDateMetric['timestamp'] = $end->getTimestamp();
                $currentDateMetric['value'] = $value;

                /* Adding value. */
                array_push($data[$metric], $currentDateMetric);
            }
        }
        return $data;
    }

}