<?php

abstract class HistogramWidget extends CronWidget
{
    use NumericWidgetTrait;

    protected static $cumulative = FALSE;

    /* -- Settings -- */
    private static $resolutionSettings = array(
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
        return array_merge(parent::getSettingsFields(), self::$resolutionSettings);
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
     * @return array
     * --------------------------------------------------
     */
    public function getDiff($multiplier=1) {
        return $this->dataManager()->compare($this->getSettings()['resolution'], $multiplier);
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

        if (isset($postData['resolution'])) {
            $resolution = $postData['resolution'];
        } else {
            $resolution = $this->getSettings()['resolution'];
        }

        return $this->dataManager()->getHistogram(
            $range, $resolution,
            $this->getSettings()['length'],
            (static::$cumulative && $this->getSettings()['type'] == 'diff')
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
