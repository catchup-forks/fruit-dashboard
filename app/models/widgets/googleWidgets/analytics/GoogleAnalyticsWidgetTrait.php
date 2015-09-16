<?php

trait GoogleAnalyticsWidgetTrait
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'resolution' => array(
            'name'       => 'Resolution',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily',
            'help_text'  => 'The resolution of the chart.'
        ),
        'property' => array(
            'name'       => 'Property',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'help_text'  => 'The widget uses this Google Analytics property (website, mobile app, etc.) for data representation.'
        )
    );
    public static $setupSettings = array('property');
    public static $criteriaSettings = array('property');

    /* Choices functions */
    public function property() {
        $properties = array();
        foreach ($this->user()->googleAnalyticsProperties as $property) {
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
        $property = $this->user()->googleAnalyticsProperties->where('id', $propertyId);
        /* Invalid property in DB. */
        if (is_null($property)) {
            return $this->user()->googleAnalyticsProperties()->first();
        }
        return $property;
    }
}

?>
