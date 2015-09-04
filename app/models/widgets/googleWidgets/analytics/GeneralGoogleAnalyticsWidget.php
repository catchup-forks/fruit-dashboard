<?php

abstract class GeneralGoogleAnalyticsWidget extends HistogramWidget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'frequency' => array(
            'name'       => 'Frequency',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily'
        ),
        'property' => array(
            'name'       => 'Property',
            'type'       => 'SCHOICE',
            'validation' => 'required'
        )
    );
    public static $setupSettings = array('property');
    public static $criteriaSettings = array('property');

    /* Choices functions */
    public function property() {
        $properties = array();
        foreach (Auth::user()->googleAnalyticsProperties as $property) {
            $properties[$property->id] = $property->name;
        }
        return $properties;
    }

    /**
     * getProperty
     * --------------------------------------------------
     * Returning the corresponding property.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
     */
    protected function getProperty() {
        $propertyId = $this->getSettings()['property'];
        $property = GoogleAnalyticsProperty::find($propertyId);
        /* Invalid property in DB. */
        if (is_null($property)) {
            return Auth::user()->googleAnalyticsProperties()->first();
        }
        return $property;
    }
}

?>
