<?php

trait FBDataCollectorTrait
{
    /**
     * getPage
     * --------------------------------------------------
     * Return the corresponding page.
     * @return FacebookPage
     * --------------------------------------------------
    */
    public function getPage()
    {
        return FacebookPage::find($this->getPageId());
    }

    /**
     * getPageId
     * --------------------------------------------------
     * Return the facebook page id.
     * @return string
     * --------------------------------------------------
    */
    public function getPageId()
    {
        return $this->criteria['page'];
    }

    /**
     * getCriteriaFields
     * Return the criteria fields for this collector.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public static function getCriteriaFields()
    {
        return array_merge(parent::getCriteriaFields(), array('page'));
    }

}
?>
