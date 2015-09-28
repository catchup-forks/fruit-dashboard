<?php

class GoogleAnalyticsBounceRateDataManager extends HistogramDataManager
{
    use GoogleAnalyticsDataManagerTrait;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getBounceRate($this->getProperty(), $this->getCriteria()['profile']));
    }
}
?>
