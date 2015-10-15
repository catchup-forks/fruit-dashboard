<?php

class GoogleAnalyticsActiveUsersDataManager extends MultipleHistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array(
        '1dayUsers',
        '7dayUsers',
        '14dayUsers',
        '30dayUsers'
    );

    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getActiveUsers(
            $this->getProfileId(),
            $this->getMetricNames(),
            $this->getOptionalParams()
        );
    }

    /**
     * getOptionalParams
     * Returning the optional parameters used by the DM.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getOptionalParams() {
        return array('dimensions' => 'ga:date');
    }

    /**
     * initializeData
     * Running custom initializer for this DM.
     */
    public function initializeData() {
        $collector = $this->getCollector();
        $profileId = $this->getProfileId();
        $data = array();
        foreach ($this->getMetricNames() as $metric) {
            $data[$metric] = $collector->getMetrics(
                $profileId,
                Carbon::now()->subDays(
                    SiteConstants::getServicePopulationPeriod()['google_analytics'])
                    ->toDateString(),
                'today',
                array($metric), $this->getOptionalParams()
            )[$metric];
        }
        $this->saveHistogram($data);

    }
}
?>
