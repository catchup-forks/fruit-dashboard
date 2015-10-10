<?php

class GoogleAnalyticsSessionsDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('sessions');
    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getSessions($this->getProfile()->id);
    }
}
?>
