<?php

abstract class HistogramWidget extends DataWidget
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
            'disabled'   => FALSE
        ),
        'length' => array(
            'name'       => 'Length',
            'type'       => 'INT',
            'validation' => 'required|min:5',
            'default'    => 15,
            'help_text'  => 'The number of data points on your histogram.'
        ),
        'type' => array(
            'name'       => 'Layout type',
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
        $data = $this->getData();
        $templateData = parent::getTemplateData();
        if ($this->isTable()) {
            return array_merge($templateData, array(
                'header'  => $data['header'],
                'content' => $data['content'],
                'data'    => $data
            ));
        }
        return array_merge($templateData, array(
            'defaultDiff'   => $this->getDiff(),
            'format'        => $this->getFormat(),
            'data'          => $data,
            'hasCumulative' => $this->hasCumulative()
        ));
    }

    /**
     * isTable
     * Returning whether or not using a table layout.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
    protected function isTable() {
        return $this->getSettings()['type'] == 'table';
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
        $settings = $this->getSettings();
        $manager = $this->data->getManager();
        $manager->setResolution(array_key_exists('resolution', $options) ? $options['resolution'] : $settings['resolution']);
        $manager->setLength(array_key_exists('length', $options) ? $options['length'] : $settings['length']);
        $manager->setRange(array_key_exists('range', $options) ? $options['range'] : array());
        $manager->setDiff(array_key_exists('diff', $options) ? $options['diff'] : FALSE);
        return $manager;
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
        return $this->data->hasCumulative();
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
        $meta['layout'] = $this->getSettings()['type']; 
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
            return 0;
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
        if ( ! is_array($postData)) {
            $postData = array();
        }
        if ($this->isTable()) {
            return $this->getTableData($postData);
        } else {
            return $this->getHistogramData($postData);
        }
    }

    /**
     * getHistogramData
     * Returning the histogram data.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    protected function getHistogramData(array $options) {
        $dmParams = array();
        /* Getting range if present. */
        if (array_key_exists('range', $options)) {
            $dmParams['range'] = $options['range'];
        }

        if (array_key_exists('resolution', $options)) {
            $dmParams['resolution'] = $options['resolution'];
        }

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
        return $this->data->getLatestValues();
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
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    public function getTableData(array $options) {
        $settings = $this->getSettings();
        $dateHeader = rtrim(ucwords($settings['resolution']), 's');
        /* Initializing table. */
        $tableData = array(
            'header' => array(
                 $dateHeader,
                 $this->getDescriptor()->name,
                 'Trend'
            ),
            'content' => array(
            )
        );

        /* Populating table data. */
        for ($i = $settings['length'] - 1; $i >= 0; --$i) {
            $now = Carbon::now();
            switch ($settings['resolution']) {
                case 'days':   $date = $now->subDays($i)->format('M-d'); break;
                case 'weeks':  $date = $now->subWeeks($i)->format('W'); break;
                case 'months': $date = $now->subMonths($i)->format('M'); break;
                case 'years':  $date = $now->subYears($i)->format('Y'); break;
                default:$date = '';
            }

            /* Calculating data. */
            $history = $this->getHistory($i);
            $value = $history['value'];

            if (isset($previousValue)) {
                $percent = ($value / $previousValue - 1) * 100;
            } else {
                $percent = 0;
            }

            /* Creating format for percent. */
            $success = $this->isSuccess($percent);
            $trendFormat = '<div class="';
            if ($success) { $trendFormat .= 'text-success';
            } else { $trendFormat .= 'text-danger'; }
            $trendFormat .= '"> <span class="fa fa-arrow-';
            if ($percent >= 0) { $trendFormat .= 'up';
            } else { $trendFormat .= 'down'; }
            $trendFormat .= '"> %.2f%%</div>';

            array_push($tableData['content'], array(
                $date,
                Utilities::formatNumber($history['value'], $this->getFormat()),
                Utilities::formatNumber($percent, $trendFormat)
            ));

            /* Saving previous value. */
            $previousValue = $value;
        }
        $tableData['content'] = array_reverse($tableData['content']);
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
        return $this->data->decode() != FALSE;
    }

}
?>
