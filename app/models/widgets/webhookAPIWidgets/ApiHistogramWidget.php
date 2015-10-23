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
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the widget.',
        ),
   );

    /* The settings to setup in the setup-wizard. */
    private static $APISetupFields = array('name', 'url');
    private static $APICriteriaFields = array('url');

    /* -- Choice functions --
    public function resolution() {
        $hourly = array('hours' => 'Hourly');
        return array_merge($hourly, parent::resolution());
    }*/

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        if ( ! $this->hasData()) {
            return self::getDefaultTemplateData($this);
        } 
        return parent::getTemplateData();
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
                          $this->user()->settings->api_key,
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

    /**
     * hasData
     * Returns whether or not there's data in the histogram.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
     public function hasData() {

         $data = $this->data->decode();
         if ($data == FALSE) {
             return FALSE;
          }
          if ( ( ! array_key_exists('datasets', $data)) ||
              ($data['datasets'] == FALSE)) {
              return FALSE ;
          }
          return TRUE;
    }

}
?>
