<?php

class TwitterFollowersDataCollector extends HistogramDataCollector
{
    public function getCurrentValue()
    {
        $collector = new TwitterDataCollector($this->user);
        return $collector->getFollowersCount();
    }
}
?>
