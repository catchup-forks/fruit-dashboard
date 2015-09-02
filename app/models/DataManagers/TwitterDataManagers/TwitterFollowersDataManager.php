<?php

class TwitterFollowersDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $collector = new TwitterDataManager($this->user);
        return $collector->getFollowersCount();
    }
}
?>
