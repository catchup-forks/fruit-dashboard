<?php

class FinancialWidget extends Widget
{
    /* -- Table specs -- */
    public static $type = 'financial';
    public static $dataRequired = TRUE;

    /* -- Settings -- */
    public static $settingsFields = array(
        'histogram' => array('name' => 'Graph on/off', 'type' => 'SCHOICE', 'validation' => 'required'),
    );
    /* The settings to setup in the setup-wizard.*/
    public static $setupSettings = array('histogram');

    /* -- Choice functions -- */
    public function histogram() {
        return array(
            0 => 'Off',
            1 => 'On'
        );
    }
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

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
