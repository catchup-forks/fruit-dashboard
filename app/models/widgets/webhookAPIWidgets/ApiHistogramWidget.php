<?php

class ApiHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    private static $APISettings = array(
        'url' => array(
            'name'       => 'POST url',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The widget data will be posted to this url.'
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the chart.'
        ),
   );

    /* The settings to setup in the setup-wizard. */
    private static $APISetupFields = array('name');
    private static $APICriteriaFields = array();

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

     // /**
     //  * collectData
     //  * --------------------------------------------------
     //  * Overrides collectData beacuse we are saving the widget
     //  *      data elsewhere (in API controller).
     //  * --------------------------------------------------
     //  */
     // public function collectData() {
     //     return TRUE;
     // }

     // /*
     //  * dataExists
     //  * Checking if datamanager exists
     //  * --------------------------------------------------
     //  * @return boolean
     //  * --------------------------------------------------
     // */
     // public function dataExists() {
     //     return  ! (is_null($this->dataManager()));
     // }
}
?>
