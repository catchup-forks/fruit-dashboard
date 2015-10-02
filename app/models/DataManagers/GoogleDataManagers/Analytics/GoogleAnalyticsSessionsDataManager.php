<?php

class GoogleAnalyticsSessionsDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metric = 'sessions';
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getSessions($this->getProperty(), $this->getCriteria()['profile']));
    }
}
?>
