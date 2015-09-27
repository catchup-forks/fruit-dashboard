<?php

abstract class TableWidget extends CronWidget
{
    /**
     * getHeader
     * Returning the table header
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getHeader() {
        return $this->dataManager()->getHeader();
    }
    /**
     * getContent
     * Returning the table articles.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getContent() {
        return $this->dataManager()->getContent();
    }
}

?>
