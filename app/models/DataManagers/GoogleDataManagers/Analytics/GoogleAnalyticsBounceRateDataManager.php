<?php

class GoogleAnalyticsBounceRateDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metric = 'bounceRate';
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getBounceRate($this->getProperty(), $this->getCriteria()['profile']));
    }
}
?>
