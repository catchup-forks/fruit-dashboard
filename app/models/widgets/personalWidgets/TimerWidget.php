<?php

class TimerWidget extends Widget
{
    /* -- Settings -- */
    private static $timerSettings = array(
        'countdown' => array(
            'name'       => 'Time',
            'type'       => 'INT',
            'validation' => 'required'
        ),
   );

    /* The settings to setup in the setup-wizard. */
    private static $timerSetupFields = array('countdown');

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$timerSettings);
     }

    /**
     * getSetupFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$timerSetupFields);
     }
}

?>
