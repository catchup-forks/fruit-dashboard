<?php

trait GoogleAnalyticsDataManagerTrait
{
    /**
     * getProperty
     * --------------------------------------------------
     * Returning the corresponding property.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    public function getProperty() {
        return GoogleAnalyticsProperty::find($this->getCriteria()['property']);
    }

    /**
     * flatData
     * --------------------------------------------------
     * Returning a flattened data.
     * @param $insightData
     * --------------------------------------------------
    */
    protected function flatData($insightData) {
        $newData = array();
        foreach ($insightData as $name=>$dataAsArray) {
            $newData[$name] = $dataAsArray[0];
        }
        return $newData;
    }
}
?>
