<?php

class FacebookPageImpressionsWidget extends GeneralFacebookWidget
{
    public function getCurrentValue() {
        $facebookCollector = new FacebookDataCollector($this->user());
        return $facebookCollector->getPageImpressions($this->getPage());
    }

}
?>
