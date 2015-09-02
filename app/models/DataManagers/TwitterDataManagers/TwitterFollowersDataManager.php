<?php

class TwitterFollowersDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $collector = new TwitterDataCollector($this->user);
        return $collector->getFollowersCount();
    }
}
?>
