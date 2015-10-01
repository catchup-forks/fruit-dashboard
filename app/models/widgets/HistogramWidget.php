<?php

abstract class HistogramWidget extends CronWidget
{
    use NumericWidgetTrait;

    protected static $cumulative   = FALSE;
    protected static $isHigherGood = TRUE;

    /* -- Settings -- */
    private static $histogramSettings = array(
        'resolution' => array(
            'name'       => 'Resolution',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days',
            'help_text'  => 'The resolution of the chart.'
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the widget.'
        ),
        'length' => array(
            'name'       => 'Length',
            'type'       => 'INT|min:5|max:15',
            'validation' => 'required',
            'default'    => 15,
            'help_text'  => 'The number of data points on your histogram.'
        ),
        'type' => array(
            'name'       => 'Histogram type',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'diff',
            'help_text'  => 'The type of your chart.'
        ),
    );

    /* -- Choice functions -- */
    public function resolution() {
        return array(
            'days'   => 'Daily',
            'weeks'  => 'Weekly',
            'months' => 'Monthly',
            'years'  => 'Yearly'
        );
    }

    /* -- Choice functions -- */
    public function type() {
        $types = array('diff' => 'Differentiated');
        if (static::$cumulative) {
            $types['sum'] = 'Cumulative';
        }
        return $types;
    }

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$histogramSettings);
     }

    /**
     * hasCumulative
     * Returns whether or not the chart has cumulative option.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
     public function hasCumulative() {
        return static::$cumulative;
     }

    /**
     * isDifferentiated
     * Returns whether or not the chart is differentiated.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
     public function isDifferentiated() {
        return (static::$cumulative && $this->getSettings()['type'] == 'diff');
     }

    /**
     * isGreen
     * Returns whether or not the diff is considered
     * good in the histogram
     * --------------------------------------------------
     * @param int $multiplier
     * @param string $resolution
     * @return boolean
     * --------------------------------------------------
     */
     public function isSuccess($multiplier=1, $resolution=null) {
        $value = $this->getDiff($multiplier, $resolution);
        return  ($value < 0) xor static::$isHigherGood;
     }

    /**
     * premiumUserCheck
     * Returns whether or not the resolution is a premium feature.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
     public function premiumUserCheck() {
        $passed = parent::premiumUserCheck();

        if ($passed === 0) {
            /* Further validation required. */
            if (static::getSettingsFields()['resolution']['default'] != $this->getSettings()['resolution']) {
                return -1;
            }
        }

        return $passed;
    }

    /**
     * getDiff
     * Comparing the current value to some historical.
     * --------------------------------------------------
     * @param int $multiplier
     * @param string $resolution
     * @return array
     * --------------------------------------------------
     */
    public function getDiff($multiplier=1, $resolution=null) {
        if (is_null($resolution)) {
            $resolution = $this->getSettings()['resolution'];
        }
        return array_values($this->dataManager()->compare($resolution, $multiplier))[0];
    }

    /**
     * getHistory
     * Returning the historical data compared to the latest.
     * --------------------------------------------------
     * @param int $multiplier
     * @param string $resolution
     * @return array
     * --------------------------------------------------
     */
    public function getHistory($multiplier=1, $resolution=null) {
        $currentValue = array_values($this->getLatestValues())[0];
        if (is_null($resolution)) {
            $resolution = $this->getSettings()['resolution'];
        }
        $value = $currentValue - $this->getDiff($multiplier, $resolution);
        try {
            $percent = ($currentValue / $value - 1) * 100;
        } catch (Exception $e) {
            $percent = 'inf';
        }
        return array(
            'value'   => $value,
            'percent' => $percent,
            'success' => $this->isSuccess($multiplier, $resolution)
        );
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

        /*$range = array(
            'start' => Carbon::createFromFormat('Y-m-d', '2015-09-04'),
            'end'   => Carbon::createFromFormat('Y-m-d', '2015-09-17')
        );*/

        if (isset($postData['resolution'])) {
            $resolution = $postData['resolution'];
        } else {
            $resolution = $this->getSettings()['resolution'];
        }

        return $this->dataManager()->getHistogram(
            $range, $resolution,
            $this->getSettings()['length'],
            $this->isDifferentiated()
        );
    }



    /**
     * getLatestValues
     * Returning the last values in the histogram.
     * --------------------------------------------------
     * @return float
     * --------------------------------------------------
     */
     public function getLatestValues() {
        return $this->dataManager()->getLatestValues();
     }

    /**
     * save
     * Adding name property if not set.
     * --------------------------------------------------
     * @param array $options
     * @return null
     * --------------------------------------------------
    */
    public function save(array $options=array()) {
        parent::save($options);
        if ( ! $this->getSettings()['name'] && $this instanceof iServiceWidget) {
            $this->saveSettings(array('name' => $this->getDefaultName()), FALSE);
        }

        return parent::save($options);
    }

    /**
     * hasData
     * Returns whether or not there's data in the histogram.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
    public function hasData() {
        return $this->dataManager()->getData() != FALSE;
    }
}
?>
