<?php

trait GoogleAnalyticsDimensionHistogramDataManagerTrait
{
    use GoogleAnalyticsDataManagerTrait;

    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = $this->getCollector();
        return $collector->getUsers($this->getProfileId());
    }

    /**
     * initialize
     * Creating, and saving data.
     */
    public function initialize() {
        /* Getting data required for the requests. */
        $collector = $this->getCollector();
        $profileId = $this->getProfileId();
        $metrics   = $this->getMetricNames();

        if (static::$cumulative) {
            /* On cumulative charts, getting the data from the past. */
            $this->initializeCumulative($collector, $metrics);
        }

        /* Building histogram. */
        /* On cumulative charts, getting the data from the past. */
        $options = $this->getOptionalParams();
        $options['dimensions'] .= ',ga:date';
        $data = $collector->getMetrics(
            $profileId,
            Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics'])->toDateString(),
            Carbon::now()->toDateString(),
            $metrics, $options, TRUE
        );
        var_dump('imsorry');
        var_dump($data);
        exit(94);
        $this->saveHistogram($data);
    }

    /**
     * initializeCumulative
     * Initializing the cumulative chart.
     * --------------------------------------------------
     * @param GoogleAnalyticsDataCollector collector
     * @param array metrics
     * @return array
     * --------------------------------------------------
     */
    private function initializeCumulative($collector, array $metrics) {
        /* Creating dates */
        $start = SiteConstants::getGoogleAnalyticsLaunchDate();
        $end = Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics']);
        /* Sending request to the API. */
        $data = $collector->getMetrics(
            $this->getProfileId(),
            $start->toDateString(), $end->toDateString(),
            $metrics,
            $this->getOptionalParams()
        );

        /* Transforming google data to entry. */
        $values = array_values($data)[0];
        $entry = $values;
        $entry['timestamp'] = $end->getTimeStamp();
        $this->collect(array('entry' => $entry));
    }

    /**
     * saveHistogram
     * --------------------------------------------------
     * Transforming and saving a histogram of values
     * in google format to our format.
     * @param array $data
     * --------------------------------------------------
    */
    private function saveHistogram(array $data) {
        /* Transformation */
        $entries = array();
        foreach ($data as $metricName=>$values) {
            foreach ($values as $dataset=>$dataValues) {
                foreach ($dataValues as $date=>$value) {
                    if ( ! array_key_exists($date, $entries)) {
                        $entries[$date] = array(
                            'timestamp' => strtotime($date)
                        );
                    }
                    $entries[$date][$dataset] = $value;
                }
            }
        }
        /* Saving entries. */
        foreach ($entries as $entry) {
            $this->collect(array(
                'entry' => $entry,
                'sum'   => $this->hasCumulative()
            ));
        }
    }

}
?>
