<?php

class GoogleAnalyticsSessionsDataManager extends GeneralGoogleAnalyticsDataManager
{
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $collector = new GoogleAnalyticsDataCollector($this->user);
        return $this->flatData($collector->getSessions($this->getProperty()));
    }
}
?>
