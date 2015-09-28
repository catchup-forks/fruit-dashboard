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
        return FacebookPage::find($this->getCriteria()['page']);
    }
}
?>
