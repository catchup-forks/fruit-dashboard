<?php

class FacebookLikesDataManager extends GeneralFacebookDataManager
{
    public function getCurrentValue() {
        /* Getting the page from settings. */
        $facebookCollector = new FacebookDataCollector($this->user);
        return $facebookCollector->getTotalLikes($this->getPage());
    }
}
?>
