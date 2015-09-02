<?php

class TwitterNewFollowersDataManager extends HistogramDataManager
{
    public function getCurrentValue() {
        $collector = new TwitterDataManager($this->user);
        /* Getting previous last data. */
        $lastData = $this->getFollowersWidget()->getLatestData();
        if (is_null($lastData)) {
            return $collector->getFollowersCount();
        }
        else {
            return $collector->getFollowersCount() - $lastData['value'];
        }
    }

    /**
     * getFollowersWidget
     * --------------------------------------------------
     * @return One of the user's twitter followers widget.
     * --------------------------------------------------
     */
    private function getFollowersWidget() {
        $descriptor = WidgetDescriptor::where('type', 'twitter_followers')->first();
        return $this->user()->widgets()->where('descriptor_id', $descriptor->id)->get()[0]->getSpecific();
    }
}
?>
