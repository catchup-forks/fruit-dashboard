<?php

class FacebookEngagedUsersWidget extends HistogramWidget
{
    public function getCurrentValue() {
        $facebookCollector = new FacebookDataCollector($this->user());
        return $facebookCollector->getEngagedUsers($this->user()->facebookPages()->first());
    }

}
?>
