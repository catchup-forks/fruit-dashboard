<?php

trait FacebookDataManagerTrait
{
    /**
     * getPage
     * --------------------------------------------------
     * Return the corresponding page.
     * @return FacebookPage
     * --------------------------------------------------
    */
    public function getPage() {
        return FacebookPage::find($this->getPageId());
    }

    /**
     * getPageId
     * --------------------------------------------------
     * Return the facebook page id.
     * @return string
     * --------------------------------------------------
    */
    public function getPageId() {
        return $this->criteria['page'];
    }
}
?>
