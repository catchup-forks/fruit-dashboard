<?php

class TwitterNewFollowersDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $collector = new TwitterDataCollector($this->user);
        /* Getting previous last data. */
        $lastData = $this->getFollowersManager()->getLatestValues();
        if (is_null($lastData)) {
            return $collector->getFollowersCount();
        }
        else {
            return $collector->getFollowersCount() - $lastData['value'];
        }
    }

    /**
     * getFollowersManager
     * --------------------------------------------------
     * @return One of the user's twitter followers widget.
     * --------------------------------------------------
     */
    private function getFollowersManager() {
        $descriptor_id = WidgetDescriptor::where('type', 'twitter_followers')->first()->id;

        return $this->user->dataManagers()->where('descriptor_id', $descriptor_id)->first();

    }
}
?>
