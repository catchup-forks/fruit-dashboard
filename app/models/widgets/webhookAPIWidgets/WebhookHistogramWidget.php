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
     * testUrl
     * Testing if there's data on the provided url.
     * --------------------------------------------------
     * @throws ServiceException
     * --------------------------------------------------
     */
    private function testUrl() {
        if ($this->dataExists()) {
            $this->data->testUrl();
        }
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

     /**
      * save
      * Overriding save to add url testing.
      * --------------------------------------------------
      * @return
      * --------------------------------------------------
      */
     public function save(array $options=array()) {
        parent::save();
        try {
            if ($this->state == 'active') {
                $this->testUrl();
            }
        } catch (ServiceException $e) {
            $this->data->setState('data_source_error', FALSE);
            parent::save();
        }
     }
}
?>
