<?php

class GoogleAnalyticsUsersDataManager extends HistogramDataManager
{
    use GoogleAnalyticsHistogramDataManagerTrait;
    protected static $metrics = array('users');
    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getUsers($this->getProfile()->id);
    }
}
?>
