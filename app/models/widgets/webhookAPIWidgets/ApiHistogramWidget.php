<?php

class ApiHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    private static $APISettings = array(
        'url' => array(
            'name'       => 'POST url',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The widget data will be posted to this url.',
            'noedit'     => TRUE
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the chart.'
        ),
   );

    /* The settings to setup in the setup-wizard. */
    private static $APISetupFields = array('name', 'url');
    private static $APICriteriaFields = array('url');

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
        return array_merge(parent::getSettingsFields(), self::$APISettings);
     }

    /**
     * getSetupFields
     * Returns the SetupFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSetupFields() {
        return array_merge(parent::getSetupFields(), self::$APISetupFields);
     }

    /**
     * getCriteriaFields
     * Returns the CriteriaFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getCriteriaFields() {
        return array_merge(parent::getCriteriaFields(), self::$APICriteriaFields);
     }

    /**
     * getWidgetApiUrl
     * Returns the POST url for the widget
     * --------------------------------------------------
     * @return (string) ($url) The url
     * --------------------------------------------------
     */
     public function getWidgetApiUrl() {
        return route('api.post', 
                    array(SiteConstants::getLatestApiVersion(),
                          $this->user()->api_key,
                          $this->id));
     }
}
?>
