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
    public function getUrl() {
        return $this->criteria['url'];
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
     * initialize
     * --------------------------------------------------
     * Saves empty array, beacuse we are saving the widget
     *      data elsewhere (in API controller).
     * --------------------------------------------------
     */
    public function initialize() {
        $this->save(array());
    }
}
?>
