<?php

class ActiveUsersDataCollector extends MultipleHistogramDataCollector
{
    use GAHistogramDataCollectorTrait;

    protected static $metrics = array(
        '1dayUsers',
        '7dayUsers',
        '14dayUsers',
        '30dayUsers'
    );

    public function getCurrentValue()
    {
        /* Getting the page from settings. */
        return $this->getCollector()->getActiveUsers(
            $this->getProfileId(),
            $this->getMetricNames(),
            $this->getOptionalParams()
        );
    }

    /**
     * getOptionalParams
     * Return the optional parameters used by the DM.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getOptionalParams()
    {
        return array('dimensions' => 'ga:date');
    }

    /**
     * initialize
     * Running custom initializer for this DM.
     */
    public function initialize()
    {
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
