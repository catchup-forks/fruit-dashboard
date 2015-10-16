<?php

abstract class HistogramWidget extends CronWidget
{
    protected static $isHigherGood = TRUE;
    use NumericWidgetTrait;

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
            'help_text'  => 'The name of the widget.',
            'disabled'   => TRUE
        ),
        'length' => array(
            'name'       => 'Length',
            'type'       => 'INT',
            'validation' => 'required|min:5',
            'default'    => 15,
            'help_text'  => 'The number of data points on your histogram.'
        ),
        'type' => array(
            'name'       => 'Histogram type',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'chart',
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
        $types = array('chart' => 'Chart', 'table' => 'Table');
        return $types;
    }

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        if ($this->getSettings()['type'] == 'table') {
            $tableData = $this->getTableData();
            return array_merge(parent::getTemplateData(), array(
                'header'  => $tableData['header'],
                'content' => $tableData['content']
            ));
        }
        return array_merge(parent::getTemplateData(), array(
            'defaultDiff'   => $this->getDiff(),
            'format'        => $this->getFormat(),
            'data'          => $this->getData(),
            'hasCumulative' => $this->hasCumulative()
        ));
    }

    /**
     * setupDataManager
     * Setting up the datamanager
     * --------------------------------------------------
     * @param array $options
     * @return DataManager
     * --------------------------------------------------
     */
     public function setupDataManager(array $options=array()) {
        $dm = $this->dataManager();
        $settings = $this->getSettings();
        $dm->setResolution(array_key_exists('resolution', $options) ? $options['resolution'] : $settings['resolution']);
        $dm->setLength(array_key_exists('length', $options) ? $options['length'] : $settings['length']);
        $dm->setRange(array_key_exists('range', $options) ? $options['range'] : array());
        $dm->setDiff(array_key_exists('diff', $options) ? $options['diff'] : FALSE);
        return $dm;
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
        return $this->dataManager()->hasCumulative();
     }

    /**
     * getTemplateMeta
     * Returning data for the gridster init template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateMeta() {
        $meta = parent::getTemplateMeta();
        $meta['general']['name'] = $this->getSettings()['name'];
        $meta['urls']['statUrl'] = route('widget.singlestat', $this->id);
        $meta['selectors']['graph'] = '[id^=chart-container]';
        return $meta;
    }

    /**
     * isSuccess
     * Returns whether or not the diff is considered
     * good in the histogram
     * --------------------------------------------------
     * @param numeric $value
     * @return boolean
     * --------------------------------------------------
     */
     public function isSuccess($value) {
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
        $dmParams = array(
            'resolution' => $resolution,
            'length'     => $multiplier + 1,
        );

        $values = $this->setupDataManager($dmParams)->compare();

        if (empty($values)) {
            return null;
        }

        return array_values($values)[0];
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
        $diff = $this->getDiff($multiplier, $resolution);
        $value = $currentValue - $this->getDiff($multiplier, $resolution);
        try {
            $percent = ($currentValue / $value - 1) * 100;
        } catch (Exception $e) {
            $percent = 'inf';
        }
        return array(
            'value'   => $value,
            'percent' => $percent,
            'success' => $this->isSuccess($percent)
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
            $range = array();
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

        $dmParams = array(
            'resolution' => $resolution,
            'range'      => array()
        );

        return $this->setupDataManager($dmParams)->getHistogram();
    }



    /**
     * getLatestValues
     * Returning the last values in the histogram.
     * --------------------------------------------------
     * @return float
     * --------------------------------------------------
     */
     public function getLatestValues() {
        $dm = $this->dataManager();
        return $dm->getLatestValues();
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
        if ($this instanceof iServiceWidget && $this->hasValidCriteria()) {
            $this->saveSettings(array('name' => $this->getDefaultName()), FALSE);
        }

        return parent::save($options);
    }

    /**
     * getTableData
     * Returns the data in table format.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTableData() {
        $settings = $this->getSettings();
        /* Initializing table. */
        $tableData = array(
            'header' => array(
                 $settings['resolution']       => 'datetime',
                 $this->getDescriptor()->name  => 'value',
                 'Trend'                       => 'trend'
            ),
            'content' => array(
            )
        );

        /* Populating table data. */
        for ($i = 0; $i < $settings['length']; ++$i) {
            $now = Carbon::now();
            switch ($settings['resolution']) {
                case 'days':   $date = $now->subDays($i)->format('d'); break;
                case 'weeks':  $date = $now->subWeeks($i)->format('W'); break;
                case 'months': $date = $now->subMonths($i)->format('l'); break;
                case 'years':  $date = $now->subYears($i)->format('Y'); break;
                default:$date = '';
            }

            /* Calculating data. */
            $history = $this->getHistory($i);

            /* Creating format for percent. */
            $success = $this->isSuccess($history['percent']);
            $trendFormat = '<div class="';
            if ($success) { $trendFormat .= 'text-success';
            } else { $trendFormat .= 'text-danger'; }
            $trendFormat .= '"> <span class="fa fa-arrow-';
            if ($success) { $trendFormat .= 'up';
            } else { $trendFormat .= 'down'; }
            $trendFormat .= '"> %.2f%%</div>';

            array_push($tableData['content'], array(
                $date,
                Utilities::formatNumber($history['value'], $this->getFormat()),
                Utilities::formatNumber($history['percent'], $trendFormat)
            ));
        }

        return $tableData;
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
