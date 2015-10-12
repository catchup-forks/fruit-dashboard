<?php

class GoogleAnalyticsPopulateData extends DataPopulator
{
    /**
     * Populating the widgets with data.
     */
    protected function populateData() {
        /* Initializing cumulative histograms. */
        $this->initializeCumulativeHDMs();

        /* Building histograms. */
        $this->buildHistograms();

        /* Running default initializer on other managers. */
        foreach ($this->dataManagers['other'] as $dataManager) {
            $dataManager->initializeData();
        }
    }

    /**
     * Getting the profile specific DataManagers
     */
    protected function getManagers() {
        $dataManagers = array(
            'histogram' => array(
                'cumulative' => array(),
                'diffed'     => array()
            ),
            'other' => array()
        );

        foreach ($this->user->dataManagers()->get() as $dataManager) {

            if ($dataManager->getDescriptor()->category == 'google_analytics' &&
                    $dataManager->getCriteria() == $this->criteria &&
                    $dataManager->getData() == FALSE) {

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
        return $dataManagers;
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

    /**
     * activateManagers
     * Setting all related widget's state to active.
     */
    protected function activateManagers() {
        foreach ($this->dataManagers['histogram'] as $type=>$managers) {
            foreach ($managers as $manager) {
                $manager->setState('active');
            }
        }
        foreach ($this->dataManagers['other'] as $type=>$manager) {
            $manager->setState('active');
        }
    }
}