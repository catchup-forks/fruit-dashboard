<?php

abstract class FinancialWidget extends Widget
{
    public static $dataRequired = TRUE;

    /* -- Settings -- */
    public static $settingsFields = array(
        'widget_type' => array(
            'name'       => 'Widget type',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'value'
        ),
    );
    /* The settings to setup in the setup-wizard.*/
    public static $setupSettings = array('widget_type');

    /* -- Choice functions -- */
    public function widget_type() {
        return array(
            'chart' => 'Chart',
            'value' => 'Current value'
        );
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /* Override this function to insert values in the widget data */
     abstract public function collectData() ;

    /**
     * getHistogram
     * --------------------------------------------------
     * Returning the histogram.
     * @return array of the histogram.
     * --------------------------------------------------
     */
    public function getHistogram() {
        return json_decode($this->data->raw_value);
    }

    /**
     * getLatestData
     * --------------------------------------------------
     * Returning the latest value in the data.
     * @return array histogram.
     * --------------------------------------------------
     */
    public function getLatestData() {
        $histogram = $this->getHistogram();
        return end($histogram);
    }
}
?>
