<?php

class GoogleAnalyticsAvgSessionDurationDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('avgSessionDuration');
    public function getCurrentValue() {
        /* Getting the profile from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getAvgSessionDuration($this->getCriteria()['profile']));
    }
}
?>
