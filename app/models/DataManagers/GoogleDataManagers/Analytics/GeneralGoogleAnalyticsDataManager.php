<?php

abstract class GeneralGoogleAnalyticsDataManager extends MultipleHistogramDataManager
{
    /**
     * getProperty
     * --------------------------------------------------
     * Returning the corresponding property.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
    */
    protected function getProperty() {
        $propertyId = $this->getCriteria()['property'];
        $property = GoogleAnalyticsProperty::find($propertyId);
        return $property;
    }
}
?>
