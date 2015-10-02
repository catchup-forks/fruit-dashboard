<?php

class GoogleAnalyticsSessionsPerUserDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metric = 'sessionsPerUser';
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getSessionsPerUser($this->getProperty(), $this->getCriteria()['profile']));
    }
}
?>
