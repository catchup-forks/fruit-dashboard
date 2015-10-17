<?php

class GoogleAnalyticsConversionsDataManager extends TableDataManager
{
    use GoogleAnalyticsDataManagerTrait;

    private static $defaultOptions = array(
        'start'       => '2005-01-01',
        'end'         => 'today',
        'max_results' => 5
    );
    private static $dimensions = array('source');
    private static $sortBy = '-ga:newUsers';

    /**
     * getOptionalParams
     * Returning the optional parameters used by the DM.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getOptionalParams() {
        return array(
            'dimensions'  => $this->getDimensions(),
            'sort'        => self::$sortBy,
        );
    }

    /**
     * getMetricNames
     * Returning the names of the metric used by the DM.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getMetricNames() {
        return array('newUsers', 'goal' . $this->criteria['goal'] . 'Completions');
    }

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
        return $collector->getMetrics(
            $this->getProfileId(),
            $start, $end,
            $this->getMetricNames(),
            array(
                'dimensions'  => $this->getDimensions(),
                'sort'        => self::$sortBy,
                'max-results' => $maxResults
            )
        );
    }

    /**
     * collect
     * Creating the table in the db.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    public function collect($options=array()) {
        $options = self::createOptions($options);
        /* Collecting and transforming data.*/
        $metricsData = array();
        foreach ($this->getMetric($options['start'], $options['end'], $options['max_results']) as $metric=>$data) {
            foreach ($data as $source=>$value) {
                $metricsData[$source][ucwords($metric)] = $value;
            }
        }

        /* Initializing table. */
        $this->initTable();

        /* Inserting rows. */
        foreach ($metricsData as $source=>$row) {
            $visitors    = array_values($row)[0];
            $completions = array_values($row)[1];
            $this->insert(array(
                $source,
                $visitors,
                $completions,
                sprintf('%.2f%%', $completions/$visitors * 100)
            ));
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
        $this->addCol('Source');
        $this->addCol('Unique visitors');
        $this->addCol('Completion');
        $this->addCol('Ratio');
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
        foreach (array_keys(self::$defaultOptions) as $key) {
            $options[$key] = self::getOption($iOptions, $key);
        }
        return $options;
    }

    /**
     * getOption
     * Returning either default, or provided option.
     * --------------------------------------------------
     * @param array $options
     * @param string $key
     * --------------------------------------------------
     */
    private static function getOption(array $options, $key) {
        if ( ! array_key_exists($key, self::$defaultOptions)) {
            /* Option not necessary. */
            return null;
        }
        return array_key_exists($key, $options) ? $options[$key] : self::$defaultOptions[$key];
    }

    /**
     * getDimensions
     * Returning the dimensions in GA format.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getDimensions() {
        return 'ga:' . implode(',ga:', self::$dimensions);
    }
}