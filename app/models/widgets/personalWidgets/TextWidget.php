<?php

class TextWidget extends Widget
{
    /* -- Settings -- */
    private static $textSettings = array(
        'text' => array(
            'name'       => 'Text',
            'type'       => 'TEXT',
            'validation' => 'required',
        ),
   );
    /* The settings to setup in the setup-wizard. */
    private static $textSetupFields = array('text');

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$textSettings);
     }

    /**
     * getSetupFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$textSetupFields);
     }

}

?>
