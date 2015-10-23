<?php

class TwitterFollowersDataManager extends HistogramDataManager
{
    protected static $cumulative = TRUE;
    public function getCurrentValue() {
        $collector = new TwitterDataCollector($this->user);
        return $collector->getFollowersCount();
    }
}
?>
