<?php

abstract class HistogramWidget extends DataWidget implements iCronWidget
{

    /* -- Settings -- */
    public static $settingsFields = array(
        'widget_type' => array(
            'name'       => 'Widget type',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'chart'
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

    abstract public function getCurrentValue();

    public function collectData() {
        try {
            /* Calculating current value */
            $newValue = $this->getCurrentValue();
        } catch (ServiceNotConnected $e) {
            return;
        }

        /* Getting previous values. */
        $currentData = $this->getHistogram();
        $lastData = end($currentData);
        $today = Carbon::now()->toDateString();

        if ($lastData && ($lastData['date'] == $today)) {
            array_pop($currentData);
        }

        /* Adding, saving data. */
        array_push($currentData, array('date' => $today, 'value' => $newValue));
        $this->data->raw_value = json_encode($currentData);
        $this->data->save();
        $this->checkIntegrity();
    }

    /**
     * getData
     * --------------------------------------------------
     * Returning the histogram.
     * @return array of the histogram.
     * --------------------------------------------------
     */
    public function getData() {
        return json_decode($this->data->raw_value, 1);
    }

    /**
     * getLatestData
     * --------------------------------------------------
     * Returning the last data in the histogram.
     * @return array of the histogram.
     * --------------------------------------------------
     */
     public function getLatestData() {
        $histogram = $this->getData();
        $lastEntry = end($histogram);
        return $lastEntry['value'];
     }

    /**
     * createDataScheme
     * --------------------------------------------------
     * Returning a deafult scheme for the data.
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function createDataScheme() {
        return json_encode(array());
    }
}
?>
