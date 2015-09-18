<?php

class FacebookPageImpressionsDataManager extends HistogramDataManager
{
    use FacebookDataManagerTrait;
    public function getCurrentValue() {
        $facebookCollector = new FacebookDataCollector($this->user);
        return $facebookCollector->getPageImpressions($this->getPage()->id);
    }

}
?>
