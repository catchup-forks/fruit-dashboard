<?php

class FacebookEngagedUsersDataManager extends HistogramDataManager
{
    use FacebookDataManagerTrait;
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $facebookCollector = new FacebookDataCollector($this->user);
        return $facebookCollector->getEngagedUsers($this->getPage()->id);
    }
}
?>
