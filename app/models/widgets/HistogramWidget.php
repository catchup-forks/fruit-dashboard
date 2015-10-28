<?php

abstract class HistogramWidget extends DataWidget
{
    /**
     * Whether or not the increasing value means good.
     *
     * @var bool
     */
    protected static $isHigherGood = TRUE;

    /* Loading the numeric traits. */
    use NumericWidgetTrait;

    /* Loading layout traits. */
    use HistogramTableLayoutTrait;
    use HistogramCountLayoutTrait;
    use HistogramChartLayoutTrait;

    /* -- Settings -- */
    private static $histogramSettings = array(
        'resolution' => array(
            'name'       => 'Time-scale',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days',
            'help_text'  => 'Set the timescale for the X axis of the chart.'
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'help_text'  => 'The name of the widget.'
        ),
        'length' => array(
            'name'       => 'Length',
            'type'       => 'INT',
            'validation' => 'required|min:5',
            'default'    => 15,
            'help_text'  => 'The number of data points on your widget.'
        ),
        'type' => array(
            'name'       => 'Layout type',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'chart',
            'help_text'  => 'The layout type of your wiget.'
        ),
    );

    /* -- Choice functions -- */
    public function resolution() 
    {
        return array(
            'days'   => 'Daily',
            'weeks'  => 'Weekly',
            'months' => 'Monthly',
            'years'  => 'Yearly'
        );
    }

    /* -- Choice functions -- */
    public function type() 
    {
        $types = array(
            'chart' => 'Line chart',
            'table' => 'Table layout'
        );
        if ($this->hasCumulative()) {
            $types['count'] = 'Count widget';
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
    public static function getSettingsFields() 
    {
        return array_merge(
            parent::getSettingsFields(),
            self::$histogramSettings
        );
    }

    /**
     * isSuccess
     * Returns whether or not the value is considered
     * good in the histogram
     * --------------------------------------------------
     * @param numeric $value
     * @return boolean
     * --------------------------------------------------
     */
    public static function isSuccess($value)
    {
        return  ($value < 0) xor static::$isHigherGood;
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
        
        $meta['layout'] = $this->getLayout();
        $meta['general']['name'] = $this->getName();

        /* Deciding which data we'll need. */
        switch ($this->getLayout()) {
            case 'table': return $this->getTableTemplateMeta($meta); break;
            case 'count': return $this->getCountTemplateMeta($meta); break;
            case 'chart': return $this->getChartTemplateMeta($meta); break;
            default: return $this->getChartTemplateMeta($meta);
        }
    
        /* Merging and returning the data. */
        return $meta;
    }

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() 
    {
        /* Getting parent data. */
        $templateData = parent::getTemplateData();

        /* Adding default data for this widget type. */
        $histogramTemplateData = array(
            'data'    => $this->getData(),
            'name'    => $this->getName(),
            'layout'  => $this->getLayout(),
            'format'  => $this->getFormat(),
            'hasData' => $this->hasData()
        );
        
        /* Deciding which data we'll need. */
        switch ($this->getLayout()) {
            case 'table': $layoutSpecificData = $this->getTableTemplateData(); break;
            case 'count': $layoutSpecificData = $this->getCountTemplateData(); break;
            case 'chart': $layoutSpecificData = $this->getChartTemplateData(); break;
            default: $layoutSpecificData = $this->getChartTemplateData();
        }

        /* Merging and returning the data. */
        return array_merge(
            $templateData,
            $histogramTemplateData,
            $layoutSpecificData
        );
    }

    /**
     * getName
     * Returning the name of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    protected function getName() 
    {
        $name = '';
        if ($this instanceof iServiceWidget && $this->hasValidCriteria()) {
            $name = $this->getServiceSpecificName();
        }
        $name .= ' - ' . $this->getSettings()['name'];
        return $name;
    }

    /**
     * getLayout
     * Returning the layout of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    protected function getLayout() 
    {
        return $this->getSettings()['type'];
    }
    
    /**
     * getResolution
     * Returning the resolution of the widget.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    protected function getResolution() 
    {
        return $this->getSettings()['resolution'];
    }

    /**
     * getLength
     * Returning the length of the widget.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
     */
    protected function getLength() 
    {
        return $this->getSettings()['length'];
    }

    /**
     * setupDataManager
     * Setting up the datamanager
     * --------------------------------------------------
     * @return DataManager
     * --------------------------------------------------
     */
    protected function setupDataManager() 
    {
        $manager = parent::setupDataManager();

        /* Default initializers. */
        $manager->setResolution($this->getResolution());
        $manager->setLength($this->getLength());

        switch ($this->getLayout()) {
            case 'table': $this->setupTableDataManager($manager); break;
            case 'count': $this->setupCountDataManager($manager); break;
            case 'chart': $this->setupChartDataManager($manager); break;
            default: ;
        }
        return $manager;
     }

    /**
     * hasCumulative
     * Returns whether or not the chart has cumulative option.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
    protected function hasCumulative()
    {
       return $this->dataManager->hasCumulative();
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
    public function getDiff($multiplier=1, $resolution=null)
    {
        if (isset($resolution)) {
            $this->dataManager->setResolution($resolution);
        }

        $this->dataManager->setLength($multiplier + 1);

        $values = $this->dataManager->compare();

        if (empty($values)) {
            return 0;
        }

        return $values;
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
    public function getHistory($multiplier=1, $resolution=null)
    {
        /* Collecting values. */
        $currentValue = array_values($this->getLatestValues())[0];
        $value = $currentValue - array_values(
            $this->getDiff($multiplier, $resolution))[0];

        try {
            $percent = ($currentValue / $value - 1) * 100;
        } catch (Exception $e) {
            $percent = 'inf';
        }

        return array(
            'value'   => $value,
            'percent' => $percent,
            'success' => static::isSuccess($percent)
        );
    }

    /**
     * getData
     * Returning the data based on layout.
     * --------------------------------------------------
     * @param array $postData
     * @return array
     * --------------------------------------------------
     */
    public function getData($postData=null)
    {
        if ( ! is_array($postData)) {
            $postData = array();
        }

        if (array_key_exists('layout', $postData)) {
            $layout = $postData['layout'];
        } else {
            $layout = $this->getLayout();
        }

        switch ($layout) {
            case 'table': return $this->getTableData($postData); break;
            case 'count': return $this->getCountData($postData); break;
            case 'chart': return $this->getChartData($postData); break;
            default: return $this->getChartData($postData);
        }

        return NULL;
    }

    /**
     * getLatestValues
     * Returning the last values in the histogram.
     * --------------------------------------------------
     * @return float
     * --------------------------------------------------
     */
     public function getLatestValues() {
        return $this->dataManager->getLatestValues();
     }


    /**
     * hasData (TODO)
     * Returns whether or not there's data in the histogram.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
    protected function hasData() {
        return $this->data == array();
    }

    /**
     * saveSettings
     * Collecting new data on change.
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=TRUE) {
        $changedFields = parent::saveSettings($inputSettings, $commit);
        if ($this->getSettings()['name'] == '') {
            $this->saveSettings(array('name' => $this->getDescriptor()->name), $commit);
        }
        return $changedFields;
    }

    /**
     * customValidator
     * Adding extra validation rules based on settings.
     * --------------------------------------------------
     * @param array $validationArray
     * @param array $inputData
     * @return array $validationArray
     * --------------------------------------------------
     */
    protected function customValidator($validationArray, $inputData) {       
        /* On table layout setting maximum values. */
        if (array_key_exists('type', $inputData) && $inputData['type'] == 'table') {
            $validationArray['length'] .= '|max:15';
        }
        return $validationArray;
    }

    /**
     * premiumUserCheck (DEPRECATED)
     * Returns whether or not the resolution is a premium feature.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
    public function premiumUserCheck()
    {
        $passed = parent::premiumUserCheck();

        if ($passed === 0) {
            /* Further validation required. */
            if (static::getSettingsFields()['resolution']['default'] != $this->getSettings()['resolution']) {
                return -1;
            }
        }

        return $passed;
    }

}
?>
