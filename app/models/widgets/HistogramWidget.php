<?php

abstract class HistogramWidget extends CronWidget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'frequency' => array(
            'name'       => 'Frequency',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily'
        ),
    );

    /* The settings to setup in the setup-wizard.*/
    public static $setupSettings = array();

    /* -- Choice functions -- */
    public function frequency() {
        return array(
            'daily'   => 'Daily',
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            'yearly'  => 'Yearly'
        );
    }

    /**
     * getLatestData
     * Returning the last data in the histogram.
     * --------------------------------------------------
     * @return float
     * --------------------------------------------------
     */
     public function getLatestData() {
        return $this->data->manager->getSpecific()->getLatestData();
     }

    /**
     * getData
     * Returning the histogram.
     * --------------------------------------------------
     * @param array $postData
     * @return array
     * --------------------------------------------------
     */
    public function getData($postData=null) {

        /* Getting range if present. */
        if (isset($postData['range'])) {
            $range = $postData['range'];
        } else {
            $range = null;
        }

        /* Looking for forced frequency. */
        if (isset($postData['frequency'])) {
            $frequency = $postData['frequency'];
        } else {
            $frequency = $this->getSettings()['frequency'];
        }

        return $this->dataManager()->getHistogram($range, $frequency);
    }

}
?>
