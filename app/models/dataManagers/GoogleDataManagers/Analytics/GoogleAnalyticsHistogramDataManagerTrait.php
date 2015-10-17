<?php

trait GoogleAnalyticsHistogramDataManagerTrait
{
    use GoogleAnalyticsDataManagerTrait;

    /**
     * initialize
     * Creating, and saving data.
     */
    public function initialize() {
        /* Getting data required for the requests. */
        $collector = $this->getCollector();
        $profileId = $this->getProfileId();
        $metrics   = $this->getMetricNames();

        if ( ! static::$cumulative) {
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
            if (is_array($values)) {
                $entry = $values;
            } else {
                $entry = array('value' => $values);
            }
            $entry['timestamp'] = $end->getTimeStamp();
            $this->collect(array('entry' => $entry, 'sum' => $this->hasCumulative()));
        }

        /* Building histogram. */
        /* On cumulative charts, getting the data from the past. */
        $data = $collector->getMetrics(
            $profileId,
            Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics'])->toDateString(),
            Carbon::now()->toDateString(),
            $metrics, array('dimensions' => 'ga:date,ga:source')
        );
        $this->saveHistogram($data);
    }

    /**
     * getCollector
     * Returning a data collector
     * --------------------------------------------------
     * @return FacebookDataCollector
     * --------------------------------------------------
     */
    protected function getCollector() {
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector;
    }

    /**
     * flatData
     * --------------------------------------------------
     * Returning a flattened data.
     * @param $insightData
     * --------------------------------------------------
    */
    public function flatData($insightData) {
        $newData = array();
        foreach ($insightData as $dataAsArray) {
            foreach ($dataAsArray as $key=>$value) {
                $newData[$key] = $value;
            }
        }
        return $newData;
    }

    /**
     * saveHistogram
     * --------------------------------------------------
     * Transforming and saving a histogram of values
     * in google format to our format.
     * @param array $data
     * --------------------------------------------------
    */
    public function saveHistogram(array $data) {
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
                'sum'   => $this->hasCumulative()
            ));
        }
    }
}
?>
