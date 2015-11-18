<?php

abstract class HistogramWidget extends DataWidget
{
    abstract public function type();

    /* Data format definer. */
    use NumericWidgetTrait;

    /* -- Settings -- */
    private static $histogramSettings = array(
        'resolution' => array(
            'name'       => 'Time-scale',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days',
            'help_text'  => 'Set the timescale for the X axis of the chart.',
            'hidden'     => true
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'help_text'  => 'The name of the widget.'
        ),
        'length' => array(
            'name'       => 'Length',
            'type'       => 'INT',
            'validation' => 'required|min:2',
            'default'    => 10,
            'help_text'  => 'The number of data points on your widget.'
        ),
        'type' => array(
            'name'       => 'Layout',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'chart',
            'help_text'  => 'The layout type of your widget.'
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
            array('Data settings' => self::$histogramSettings)
        );
    }

    /**
     * getTemplateMeta
     * Return data for the gridster init template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateMeta()
    {
        $meta = parent::getTemplateMeta();

        $meta['layout'] = $this->getLayout();
        $meta['general']['name'] = $this->getName();
        $meta['urls']['statUrl'] = route('widget.singlestat', $this->id);
        
        /* Chart specific meta. */
        $meta['selectors']['activeLayout'] = '#widget-layout-' . $this->getLayout() . '-' . $this->id;
    
        /* Count specific meta. */
        if (in_array('count', $this->type())) {
            $meta['selectors']['count'] = 'count-' . $this->id;
        }

        return $meta;
    }

    /**
     * getTemplateData
     * Return the mostly used values in the template.
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
            'name'            => $this->getName(),
            'defaultLayout'   => $this->getLayout(),
            'possibleLayouts' => $this->type(),
            'format'          => $this->getFormat(),
            'hasData'         => empty($this->activeHistogram),
            'data'            => array()
        );

        /* Adding all layout data. */
        foreach ($this->type() as $layout=>$name) {
            $histogramTemplateData['data'][$layout] = $this->getData(array('layout' => $layout));
        }
        
        /* Merging and returning the data. */
        return array_merge(
            $templateData,
            $histogramTemplateData
        );
    }

    /**
     * getName
     * Return the name of the widget.
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
     * Return the layout of the widget.
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
     * Return the resolution of the widget.
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
     * Return the length of the widget.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
     */
    protected function getLength()
    {
        return $this->getSettings()['length'];
    }

    /**
     * saveSettings
     * Collecting new data on change.
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=true) {
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
        if (array_key_exists('type', $inputData)) {
            if ($inputData['type'] == 'table') {
                /* Table rows must be less than 15. */
                $validationArray['length'] .= '|max:15';

            } else if ($inputData['type'] == 'count') {
                $validationArray['length'] = 'integer|max:12';
            }
        }
        return $validationArray;
    }

    /**
     * getData
     * Build the chart data.
     * --------------------------------------------------
     * @param array $postData
     * @return array
     * --------------------------------------------------
    */
    protected function getData(array $postData=array())
    {
        if (empty($postData)) {
            $postData = array();
        }

        if (array_key_exists('layout', $postData)) {
            $layout = $postData['layout'];
        } else {
            $layout = $this->getLayout();
        }

        /* Building the histogram. */
        $this->setActiveHistogram($this->buildHistogramEntries());

        /* Creating getData function name. */
        $fn = 'get' . Utilities::underScoreToCamelCase($layout . '_data');
        return $this->$fn($postData);
    }

    /**
     * onCreate
     * Applying settings.
     * --------------------------------------------------
     * @param array $attributes
     * --------------------------------------------------
     */
    protected function onCreate()
    {
        parent::onCreate();

        $this->setResolution($this->getResolution());
        $this->setLength($this->getLength());
    }

}
?>
