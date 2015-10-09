<?php

class GoogleAnalyticsSessionsPerUserDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('sessionsPerUser');
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getSessionsPerUser($this->getCriteria()['profile']));
    }
}
?>
