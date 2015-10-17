<?php

trait FacebookDataManagerTrait
{
    /**
     * getPage
     * --------------------------------------------------
     * Returning the corresponding page.
     * @return FacebookPage
     * --------------------------------------------------
    */
    public function getPage() {
        return FacebookPage::find($this->getPageId());
    }

    /**
     * getPageId
     * --------------------------------------------------
     * Returning the facebook page id.
     * @return string
     * --------------------------------------------------
    */
    public function getPageId() {
        return $this->criteria['page'];
    }
}
?>
