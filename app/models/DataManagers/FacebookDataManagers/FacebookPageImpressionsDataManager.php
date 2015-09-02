<?php

class FacebookPageImpressionsDataManager extends GeneralFacebookDataManager
{
    public function getCurrentValue() {
        $facebookCollector = new FacebookDataCollector($this->user);
        return $facebookCollector->getPageImpressions($this->getPage());
    }

}
?>
