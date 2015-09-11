<?php

abstract class GeneralFacebookDataManager extends HistogramDataManager
{
    /**
     * getPage
     * --------------------------------------------------
     * Returning the corresponding page.
     * @return FacebookPage
     * --------------------------------------------------
    */
    protected function getPage() {
        return $this->getCriteria()['page'];
    }
}
?>
