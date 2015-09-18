<?php

class GoogleAnalyticsTopSourcesDataManager extends TableDataManager
{
    use GoogleAnalyticsDataManagerTrait;
    private static $start = '30daysAgo';
    private static $end = 'today';
    private static $dimensions = 'source';
    private static $sortBy = '-ga:sessions';
    private static $maxResults = '15';
    private static $metrics = array('sessions', 'users', 'hits');

    private function getMetric() {
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getMetrics($this->getProperty(), self::$start, self::$end, self::$metrics, array('dimensions' => 'ga:' . self::$dimensions, 'sort' => self::$sortBy, 'max-results' => self::$maxResults));
    }

    public function collectData() {
        /* Collecting and transforming data.*/
        $metricsData = array();
        foreach ($this->getMetric() as $metric=>$data) {
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