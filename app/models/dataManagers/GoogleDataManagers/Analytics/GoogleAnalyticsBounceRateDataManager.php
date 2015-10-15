<?php

class GoogleAnalyticsBounceRateDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('bounceRate');
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getBounceRate($this->getProfileId());
    }
}
?>
