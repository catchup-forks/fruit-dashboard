<?php

class GoogleAnalyticsTopSourcesDataManager extends TableDataManager
{
    use GoogleAnalyticsDataManagerTrait;
    private static $defaultStart = '2005-01-01';
    private static $defaultEnd = 'today';
    private static $defaultMaxResults = '15';
    private static $dimensions = 'source';
    private static $sortBy = '-ga:sessions';
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

    /**
     * collectData
     * Creating the table in the db.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    public function collectData($options=array()) {
        $options = self::createOptions($options);
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

    /**
     * initTable
     * Reinitializing the table.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    private function initTable() {
        /* Clearing table */
        $this->clearTable();

        /* Adding cols. */
        $this->addCol('Metric');
        foreach (self::$metrics as $metric) {
            $this->addCol(ucwords($metric));
        }
    }

    /**
     * createOptions
     * Returning a valid options array.
     * --------------------------------------------------
     * @param array $iOptions
     * @return array
     * --------------------------------------------------
     */
    private static function createOptions(array $iOptions=array()) {
        $options = array();
        if (array_key_exists('start', $iOptions)) {
            $options['start'] = $iOptions['start'];
        } else {
            $options['start'] = self::$defaultStart;
        }

        if (array_key_exists('end', $iOptions)) {
            $options['end'] = $iOptions['end'];
        } else {
            $options['end'] = self::$defaultEnd;
        }

        if (array_key_exists('max_results', $iOptions)) {
            $options['max_results'] = $iOptions['max_results'];
        } else {
            $options['max_results'] = self::$defaultMaxResults;
        }

        return $options;
    }

}