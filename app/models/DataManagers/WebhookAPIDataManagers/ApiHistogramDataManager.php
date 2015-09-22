<?php

class ApiHistogramDataManager extends MultipleHistogramDataManager
{
    /**
     * getUrl
     * --------------------------------------------------
     * Returns the POST url.
     * @return string
     * --------------------------------------------------
    */
    protected function getUrl() {
        return $this->getSettings()['url'];
    }

    /**
     * getCurrentValue
     * --------------------------------------------------
     * Returns empty array, beacuse we are saving the widget
     *      data elsewhere (in API controller).
     * @return array
     * --------------------------------------------------
     */
    public function getCurrentValue() {
        return array();
    }

    /**
     * initializeData
     * --------------------------------------------------
     * Saves empty array, beacuse we are saving the widget
     *      data elsewhere (in API controller).
     * --------------------------------------------------
     */
    public function initializeData() {
        $this->saveData(array());
    }
}
?>
