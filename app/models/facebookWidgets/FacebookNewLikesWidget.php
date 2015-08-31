<?php

class FacebookNewLikesWidget extends HistogramWidget
{
    public function getCurrentValue() {
        $collector = new FacebookDataCollector($this->user());
        /* Getting previous last data. */
        $lastData = $this->getLatestData();
        if (is_null($lastData)) {
            return $collector->getTotalLikes($this->user()->facebookPages()->first());
        }
        else {
            return $collector->getTotalLikes($this->user()->facebookPages()->first()) - $lastData;
        }
    }

}
?>
