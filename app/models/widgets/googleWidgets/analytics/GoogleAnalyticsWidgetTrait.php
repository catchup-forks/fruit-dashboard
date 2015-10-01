<?php

trait GoogleAnalyticsWidgetTrait
{
    /* -- Settings -- */
    private static $profileSettings = array(
        'profile' => array(
            'name'       => 'Profile',
            'type'       => 'SCHOICEOPTGRP',
            'validation' => 'required',
        ),
        'property' => array(
            'name'      => 'Property',
            'type'      => 'TEXT',
            'hidden'    => TRUE
        ),
    );
    private static $profile = array('profile');
    private static $profileProperty = array('profile', 'property');

    /* Choices functions */
    public function profile() {
        $properties = array();
        $collector = new GoogleAnalyticsDataCollector($this->user());
        foreach ($this->user()->googleAnalyticsProperties as $property) {
            $profiles = array();
            foreach ($collector->getProfiles($property) as $profile) {
                $profiles[$profile->id] = $profile->name;
            }
            $properties[$property->name] = $profiles;
        }
        return $properties;
    }


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
        return array_merge(parent::getSettingsFields(), self::$profileSettings);
    }

    /**
     * getSetupFields
     * --------------------------------------------------
     * Updating setup fields.
     * @return array
     * --------------------------------------------------
     */
    public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$profile);
    }

    /**
     * getCriteriaFields
     * --------------------------------------------------
     * Updating criteria fields.
     * @return array
     * --------------------------------------------------
     */
    public static function getCriteriaFields() {
        return array_merge(parent::getSetupFields(), self::$profileProperty);
    }

    /**
     * getProperty
     * --------------------------------------------------
     * Returning the corresponding property.
     * @return GoogleAnalyticsProperty
     * --------------------------------------------------
     */
    public function getProperty() {
        $propertyId = $this->getSettings()['property'];
        $property = $this->user()->googleAnalyticsProperties()->where('id', $propertyId)->first();
        /* Invalid property in DB. */
        if (is_null($property)) {
            return $this->user()->googleAnalyticsProperties()->first();
        }
        return $property;
    }

    /**
     * getDefaultName
     * Returning the default name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getDefaultName() {
        return $this->getProperty()->name . ' - ' . $this->descriptor->name;
    }

     /**
      * Save | Override hidden setting.
      * --------------------------------------------------
      * @param array $options
      * @return Saves the widget
      * --------------------------------------------------
     */
     public function save(array $options=array()) {
        /* Call parent save */
        parent::save($options);

        $collector = new GoogleAnalyticsDataCollector($this->user());
        foreach ($this->user()->googleAnalyticsProperties as $property) {
            $profiles = array();
            foreach ($collector->getProfiles($property) as $profile) {
                if ($profile->id == $this->getSettings()['profile']) {
                    $this->saveSettings(array('property' => $property->id), FALSE);
                    return parent::save();
                }
            }
        }

         /* Return */
         return parent::save();
     }

}

?>
