<?php

class GoogleAnalyticsSessionsDataManager extends MultipleHistogramDataManager
{
    use GoogleAnalyticsHistogramBySourceDataManagerTrait;
    protected static $metrics = array('sessions');
    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getSessions($this->getProfileId());
    }
}
?>
