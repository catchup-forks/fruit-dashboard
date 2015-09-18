<?php

trait GoogleAnalyticsWidgetTrait
{
    /* -- Settings -- */
    private static $propertySettings = array(
        'property' => array(
            'name'       => 'Property',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'help_text'  => 'The widget uses this Google Analytics property (website, mobile app, etc.) for data representation.'
        )
    );
    private static $property = array('property');

    /**
     * getConnectorClass
     * --------------------------------------------------
     * Returns the connector class for the widgets.
     * @return string
     * --------------------------------------------------
     */
    public function getConnectorClass() {
        return 'GoogleAnalyticsConnector';
    }

    /**
     * getSettingsFields
     * --------------------------------------------------
     * Returns the updated settings fields
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$propertySettings);
    }

    /**
     * getSetupFields
     * --------------------------------------------------
     * Updating setup fields.
     * @return array
     * --------------------------------------------------
     */
    public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$property);
    }

    /**
     * getCriteriaFields
     * --------------------------------------------------
     * Updating criteria fields.
     * @return array
     * --------------------------------------------------
     */
    public static function getCriteriaFields() {
        return array_merge(parent::getSetupFields(), self::$property);
    }

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
