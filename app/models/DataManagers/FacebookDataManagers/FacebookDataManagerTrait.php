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
    protected function getPage() {
        return $this->getCriteria()['page'];
    }
}
?>
