<?php

class GoogleAnalyticsPopulateData
{
    /**
     * The user object.
     *
     * @var User
     */
    private $user = null;

    /**
     * The dataManagers.
     *
     * @var array
     */
    private $dataManagers = array();

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
        /* Init */
        Log::info("Starting data collection at " . Carbon::now()->toDateTimeString());
        $time = microtime(TRUE);
        $this->user     = User::find($data['user_id']);
        $this->criteria = $data['criteria'];

        /* Getting managers. */
        $this->getManagers();

        /* Running data collection. */
        $this->populateData();

        /* Finish */
        Log::info("Data collection finished and it took " . (microtime(TRUE) - $time) . " seconds to run.");

        $job->delete();
    }

    /**
     * Populating the widgets with data.
     */
    private function populateData() {
        /* Initializing cumulative histograms. */
        $this->initializeCumulativeHDMs();

        /* Building histograms. */
        $this->buildHistograms();

        /* Running default initializer on other managers. */
        foreach ($this->dataManagers['other'] as $dataManager) {
            $dataManager->initializeData();
            $dataManager->setWidgetsState('active');
        }
    }

    /**
     * Getting the profile specific DataManagers
     */
    private function getManagers() {
        $dataManagers = array(
            'histogram' => array(
                'cumulative' => array(),
                'diffed'     => array()
            ),
            'other' => array()
        );

        foreach ($this->user->dataManagers()->get() as $dataManager) {
            $dataManager->data->raw_value = json_encode(array());
            $dataManager->data->save();

            if ($dataManager->getDescriptor()->category == 'google_analytics' && $dataManager->getCriteria() == $this->criteria) {
                if ($dataManager instanceof HistogramDataManager &&
                        empty($dataManager->getOptionalParams())) {
                    if ($dataManager->hasCumulative()) {
                        $dataManagers['histogram']['cumulative'][$dataManager->getDescriptor()->type] = $dataManager;
                    } else {
                        $dataManagers['histogram']['diffed'][$dataManager->getDescriptor()->type] = $dataManager;
                    }
                } else {
                    $dataManagers['other'][$dataManager->getDescriptor()->type] = $dataManager;
                }
            }
        }
        $this->dataManagers = $dataManagers;
    }

    /**
     * initializeCumulativeHDMs
     * Setting the first values of all cumulative dms.
     */
    private function initializeCumulativeHDMs() {
        /* Preparing optimized loader. */
        $loader = new GoogleAnalyticsOptimizedLoader(
            $this->user,
            $this->dataManagers['histogram']['cumulative']
        );

        $loader->execute(
            SiteConstants::getGoogleAnalyticsLaunchDate(),
            Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics'])
        );
    }

    /**
     * buildHistograms
     * Collecting data for all histogram managers
     */
    private function buildHistograms() {
        $managers = array();
        foreach (array_merge(
                $this->dataManagers['histogram']['cumulative'],
                $this->dataManagers['histogram']['diffed']
            ) as $type=>$dataManager) {
            array_push($managers, $dataManager);
        }

        $loader = new GoogleAnalyticsOptimizedLoader($this->user, $managers);

        $loader->execute(
            Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics']),
            Carbon::now(),
            'ga:date'
        );
    }

}