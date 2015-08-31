<?php

class FacebookNewLikesWidget extends GeneralFacebookWidget
{
    public function getCurrentValue() {
        $collector = new FacebookDataCollector($this->user());
        /* Getting previous last data. */
        $lastData = $this->getLatestData();
        if (is_null($lastData)) {
            return $collector->getTotalLikes($this->getPage());
        }
        else {
            return $collector->getTotalLikes($this->getPage()) - $lastData['value'];
        }
    }

}
?>
