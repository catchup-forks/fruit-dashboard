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
    );
    private static $profile = array('profile');
    private static $profileProperty = array('profile');

    /* Choices functions */
    public function profile() {
        $profiles = array();
        foreach ($this->user()->googleAnalyticsProperties as $property) {
            $profiles[$property->name] = array();
            foreach ($property->profiles as $profile) {
                $profiles[$property->name][$profile->profile_id] = $profile->name;
            }
        }
        return $profiles;
    }

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'propertyName' => $this->getProperty()->name
        ));
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
        $profileId = $this->getSettings()['profile'];
        $profile = $this->user()->googleAnalyticsProfiles()->where('profile_id', $profileId)->first();
        /* Invalid profile in DB. */
        if (is_null($profile)) {
            return null;
        }
        $property = $this->user()->googleAnalyticsProperties()->where('property_id', $profile->property_id)->first();
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
        return $this->getProperty()->name . ' - ' . $this->getDescriptor()->name;
    }

}

?>
