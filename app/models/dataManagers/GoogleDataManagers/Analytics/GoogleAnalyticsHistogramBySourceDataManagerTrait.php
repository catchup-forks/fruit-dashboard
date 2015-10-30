<?php

trait GoogleAnalyticsHistogramBySourceDataManagerTrait
{
    use GoogleAnalyticsHistogramDataManagerTrait;

    /**
     * getOptionalParams
     * Returns the optional parameters globally used,
     * during data collection.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getOptionalParams() {
        $metric = $this->getMetricNames()[0];
        return array(
            'dimensions'  => 'ga:source',
            'sort'        => '-ga:' . $metric
        );
    }

    /**
     * initialize
     * Creating, and saving data.
     */
    public function initialize() {
        /* Getting initial values. */
        $collector = $this->getCollector();
        $profileId = $this->getProfileId();
        $metrics   = $this->getMetricNames();
    
        /* Querying initial data. */
        $start = SiteConstants::getGoogleAnalyticsLaunchDate();
        $end = Carbon::now()->subDays(SiteConstants::getServicePopulationPeriod()['google_analytics']);
        $data = $collector->getMetrics(
            $profileId,
            $start->toDateString(), $end->toDateString(),
            $metrics,
            $this->getOptionalParams()
        );
    
        /* Saving initial data */
        $this->saveInitialData($data, $end->getTimestamp());

        /* Collecting diff data. */
        $params = $this->getOptionalParams();
        $params['dimensions'] .= ',ga:date';
        
        $start = $end;
        $end = Carbon::now();
        $data = $collector->getMetrics(
            $profileId,
            $start->toDateString(), $end->toDateString(),
            $metrics, $params, TRUE
        );
        $this->saveDiffData($data, $start, $end);

        return;
    }

    /**
     * saveInitialData
     * Transforming and saving the initial data.
     * --------------------------------------------------
     * @param array $data   
     * @param Carbon $timestamp
     * --------------------------------------------------
     */
    private function saveInitialData($data, $timestamp) {
        /* Building initial entry. */
        $entry = array_values($data)[0];
        $entry['timestamp'] = $timestamp;
        $this->save(self::transformData(array($entry)));
    }

    /**
     * saveDiffData
     * Transforming and saving the initial data.
     * --------------------------------------------------
     * @param array $data   
     * @param Carbon $start
     * @param Carbon $end
     * --------------------------------------------------
     */
    private function saveDiffData($data, $start, $end) {
        if (empty($data)) {
            return;
        }
        $entries = array();
        foreach ($data as $dataPoint) {
            $source = $dataPoint[0];
            $date   = $dataPoint[1];
            $value  = $dataPoint[2];
            
            /* Creating date attribute if did not exist. */
            if ( ! array_key_exists($date, $entries)) {
                $entries[$date] = array(
                    'timestamp' => strtotime($date)
                );
            }
            
            /* Adding source value. */
            $entries[$date][$source] = $value;
        }
        $diff = $end->diffInDays($start);

        /* Populating previous values. */
        $end->addDay();
        for ($i = 0; $i < $diff; ++$i) {
            /* Creating a copy of the $end object. */
            $date = $end->subDay()->format('Ymd');
            if ( ! array_key_exists($date, $entries)) {
                $entries[$date] = array(
                    'timestamp' => strtotime($date)
                );
            }
        }
    
        /* Sorting the entries by date. */
        ksort($entries);
        
        /* Creating DB ready entries. */ 
        foreach ($entries as $entry) {
            $this->collect(array('entry' => $entry, 'sum' => TRUE));
        }
    
    }

}
?>
