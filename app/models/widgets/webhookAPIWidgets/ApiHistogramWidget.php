<?php

class ApiHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    private static $APISettings = array(
        'url' => array(
            'name'       => 'POST url',
            'type'       => 'TEXT',
            'help_text'  => 'The widget data will be posted to this url.',
            'disabled'   => TRUE
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

     /**
      * Save | Override to save the URL
      * --------------------------------------------------
      * @param array $options
      * @return Saves the widget
      * --------------------------------------------------
     */
     public function save(array $options=array()) {
         /* Call parent save */
         parent::save($options);

         /* Add URL value to settings */
         $this->saveSettings(array(
            'url' => $this->getWidgetApiUrl(),
         ), FALSE);

         /* Return */
         return parent::save();
     }

}
?>
