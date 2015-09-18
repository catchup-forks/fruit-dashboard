<?php

class GoogleAnalyticsAvgSessionDurationDataManager extends MultipleHistogramDataManager
{
    use GoogleAnalyticsDataManagerTrait;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getAvgSessionDuration($this->getProperty()));
    }
}
?>
