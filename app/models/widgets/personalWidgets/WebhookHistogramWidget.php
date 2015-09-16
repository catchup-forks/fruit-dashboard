<?php

class WebhookHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    protected static $webhookSettings = array(
        'url' => array(
            'name'       => 'Webhook url',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The widget data will be populated from data on this url.'
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the chart.'
        ),
   );

    /* The settings to setup in the setup-wizard. */
    protected static $webhookSetupFields = array('name', 'url');
    protected static $webhookCriteriaFields = array('url');

    /* -- Choice functions -- */
    public function resolution() {
        return array(
            'hourly'  => 'Hourly',
            'daily'   => 'Daily',
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            'yearly'  => 'Yearly'
        );
    }

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
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$webhookSetupFields);
     }

    /**
     * getCriteriaSettings
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getCriteriaSettings() {
        return array_merge(parent::getCriteriaSettings(), self::$webhookCriteriaFields);
     }
}
?>
