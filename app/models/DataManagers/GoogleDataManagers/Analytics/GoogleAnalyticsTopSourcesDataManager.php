<?php

class GoogleAnalyticsTopSourcesDataManager extends TableDataManager
{
    use GoogleAnalyticsDataManagerTrait;
    private static $start = '2005-01-01';
    private static $end = 'today';
    private static $dimensions = 'source';
    private static $sortBy = '-ga:sessions';
    private static $maxResults = '15';
    private static $metrics = array('sessions', 'users', 'hits');


    /**
     * getMetric
     * Calculates and returns the metric.
     * --------------------------------------------------
     * @param date $start
     * @param date $end
     * @param int $maxResults
     * @return array
     * --------------------------------------------------
     */
    private function getMetric($start, $end, $maxResults) {
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getMetrics($this->getProperty(), $start, $end, self::$metrics, array('dimensions' => 'ga:' . self::$dimensions, 'sort' => self::$sortBy, 'max-results' => $maxResults));
    }

    public function collectData($options=array()) {
        /* Collecting and transforming data.*/
        $metricsData = array();
        foreach ($this->getMetric($options['start'], $options['end'], $options['max_results']) as $metric=>$data) {
            foreach ($data as $source=>$value) {
                $metricsData[$source][ucwords($metric)] = $value[0];
            }
        }

        /* Initializing table. */
        $this->initTable();

        /* Inserting rows. */
        foreach ($metricsData as $metric=>$row) {
            $this->insert(array_merge(array('Metric' => $metric), $row));
        }
    }

    private function initTable() {
        /* Clearing table */
        $this->clearTable();

        /* Adding cols. */
        $this->addCol('Metric');
        foreach (self::$metrics as $metric) {
            $this->addCol(ucwords($metric));
        }
    }
}