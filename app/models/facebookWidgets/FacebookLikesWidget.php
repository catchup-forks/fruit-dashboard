<?php

class FacebookLikesWidget extends HistogramWidget
{
    public function getCurrentValue() {
        $facebookCollector = new FacebookDataCollector($this->user());
        return $facebookCollector->getTotalLikes($this->user()->facebookPages()->first());
    }

}
?>
