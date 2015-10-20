<?php

class GoogleAnalyticsUsersDataManager extends MultipleHistogramDataManager
{
    use GoogleAnalyticsHistogramBySourceDataManagerTrait;

    protected static $metrics = array('newUsers');
    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $collector->getUsers($this->getProfileId());
    }
}
?>
