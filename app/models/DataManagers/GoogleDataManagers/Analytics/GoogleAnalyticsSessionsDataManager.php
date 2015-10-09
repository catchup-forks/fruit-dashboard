<?php

class GoogleAnalyticsSessionsDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('sessions');
    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getSessions($this->getCriteria()['profile']));
    }
}
?>
