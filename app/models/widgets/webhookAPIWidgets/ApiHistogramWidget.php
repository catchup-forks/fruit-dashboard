<?php

class ApiHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    private static $APISettings = array(
        'url' => array(
            'name'       => 'POST url',
            'type'       => 'TEXT',
            'help_text'  => 'The widget data will be posted to this url.',
            'disabled'   => true
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
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        if ( ! $this->hasData()) {
            return array_merge(self::getDefaultTemplateData($this), array(
                'hasData' => $this->hasData()
            ));
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
     * saveSettings
     * Override to save the URL
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
     */
     public function saveSettings(array $inputSettings, $commit=true) {
         $inputSettings['url'] = $this->getWidgetApiUrl();
         return parent::saveSettings($inputSettings, $commit);
    }

}
?>
