<?php

class WebhookHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    private static $webhookSettings = array(
        'url' => array(
            'name'       => 'Webhook url',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The widget data will be populated from data on this url.'
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'help_text'  => 'The name of the widget.',
        ),
   );

    /* The settings to setup in the setup-wizard. */
    private static $webhookSetupFields = array('name', 'url');
    private static $webhookCriteriaFields = array('url');

    /* -- Choice functions --
    public function resolution() {
        $hourly = array('hours' => 'Hourly');
        return array_merge($hourly, parent::resolution());
    }*/

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$webhookSettings);
     }

    /**
     * getSetupFields
     * Returns the SetupFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$webhookSetupFields);
     }

    /**
     * getCriteriaFields
     * Returns the CriteriaFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getCriteriaFields() {
        return array_merge(parent::getCriteriaFields(), self::$webhookCriteriaFields);
     }
}
?>
