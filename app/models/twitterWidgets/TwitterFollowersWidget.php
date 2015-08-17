<?php

class TwitterFollowersWidget extends HistogramWidget
{
    public function getCurrentValue() {
        $collector = new TwitterDataCollector($this->user());
        return $collector->getFollowersCount();
    }
}
?>
