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
        $pageId = $this->getCriteria()['page'];
        $page = FacebookPage::find($pageId);
        return $page;
    }
}
?>
