<?php

trait GAHistogramDataCollectorTrait
{
    use GADataCollectorTrait;

    /**
     * initialize
     * Creating, and saving data.
     */
    public function initialize() {
        /* Getting data required for the requests. */
        $collector = $this->getCollector();
        $profileId = $this->getProfileId();
        $metrics   = $this->getMetricNames();

        if ($this->isCumulative()) {
            /* On cumulative charts, getting the data from the past. */
            $start = SiteConstants::getGoogleAnalyticsLaunchDate();
            $end = Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics']);
            $data = $collector->getMetrics(
                $profileId,
                $start->toDateString(), $end->toDateString(),
                $metrics,
                $this->getOptionalParams()
            );
            $values = array_values($data)[0];
            $entry = array('value' => $values);
            $entry['timestamp'] = $end->getTimeStamp();
            $this->collect(array('entry' => $entry, 'sum' => $this->isCumulative()));
        }

        /* Building histogram. */
        /* On cumulative charts, getting the data from the past. */
        $data = $collector->getMetrics(
            $profileId,
            Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics'])->toDateString(),
            Carbon::now()->toDateString(),
            $metrics, array('dimensions' => 'ga:date')
        );
        $this->saveHistogram($data);
    }

    /**
     * saveHistogram
     * --------------------------------------------------
     * Transforming and saving a histogram of values
     * in google format to our format.
     * @param array $data
     * --------------------------------------------------
    */
    protected function saveHistogram(array $data) {
        /* Transformation */
        $entries = array();
        foreach ($data as $metricName=>$values) {
            foreach ($values as $date=>$value) {
                if ( ! array_key_exists($date, $entries)) {
                    $entries[$date] = array(
                        'timestamp' => strtotime($date)
                    );
                }
                $entries[$date][$metricName] = $value;
            }
        }
        /* Saving entries. */
        foreach ($entries as $entry) {
            $this->collect(array(
                'entry' => $entry,
                'sum'   => $this->isCumulative()
            ));
        }
    }
}
?>
