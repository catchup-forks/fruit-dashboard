<?php

class GoogleAnalyticsAvgSessionDurationDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('avgSessionDuration');
    public function getCurrentValue() {
        /* Getting the profile from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getAvgSessionDuration($this->getProfile()->id);
    }
}
?>
