<?php

class GoogleAnalyticsUsersDataManager extends MultipleHistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('newUsers');
    protected static $cumulative = TRUE;
    private static $dimensions = array('source');
    private static $sortBy = '-ga:users';
    private static $maxResults = 5;

    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getUsers($this->getProfileId());
    }

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
            'max-results'  => 10
        );
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

    /**
     * saveHistogram
     * Transforming data to multiplehistogram format.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public function saveHistogram(array $data) {
        $entries = array();
        foreach ($data as $metricName=>$values) {
            foreach ($values as $date=>$value) {
                $entry = $value;
                $entry['timestamp'] = strtotime($date);
                array_push($entries, $entry);
            }
        }
        /* Saving entries. */
        foreach ($entries as $entry) {
            $this->collectData(array(
                'entry' => $entry,
                'sum'   => $this->hasCumulative()
            ));
        }
    }
}
?>
