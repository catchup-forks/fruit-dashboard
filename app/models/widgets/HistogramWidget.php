<?php

abstract class HistogramWidget extends CronWidget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'resolution' => array(
            'name'       => 'Resolution',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily',
            'help_text'  => 'The resolution of the chart.'
        ),
    );

    /* The settings to setup in the setup-wizard.*/
    public static $setupSettings = array();

    /* -- Choice functions -- */
    public function resolution() {
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
     * premiumUserCheck
     * Returns whether or not the resolution is a premium feature.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
     public function premiumUserCheck() {
        /* Premium users can see everything. */
        if ($this->user()->subscription->getSubscriptionInfo()['PE']) {
            return TRUE;
        }

        /* The resolution is set to default. */
        if (static::$settingsFields['resolution']['default'] == $this->getSettings()['resolution']) {
            return TRUE;
        }

        return FALSE;
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

        /* Looking for forced resolution. */
        if (isset($postData['resolution'])) {
            $resolution = $postData['resolution'];
        } else {
            $resolution = $this->getSettings()['resolution'];
        }

        return $this->dataManager()->getHistogram($range, $resolution);
    }

}
?>
