<?php

class GoogleAnalyticsUsersDataCollector extends HistogramDataCollector
{
    use GoogleAnalyticsHistogramBySourceDataManagerTrait;

    protected static $metrics = array('newUsers');
    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        return $this->getCollector()->getUsers($this->getProfileId());
    }
}
?>
