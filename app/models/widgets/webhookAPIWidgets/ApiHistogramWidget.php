<?php

class ApiHistogramWidget extends HistogramWidget
{
    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramChartLayoutTrait;
    use HistogramTableLayoutTrait;

    /* Data selector. */
    protected static $dataTypes = array('api');

    /* Choice functions */
    public function type()
    {
        return array(
            SiteConstants::LAYOUT_MULTI_LINE        => 'All your data ',
            SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'Your data summarized, and diff.',
            SiteConstants::LAYOUT_TABLE             => 'Table layout',
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_MULTI_LINE        => 'getChartData',
        SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'getChartData',
        SiteConstants::LAYOUT_TABLE             => 'getTableData',
    );

    /* -- Settings -- */
    private static $APISettings = array(
        'url' => array(
            'name'       => 'POST url',
            'type'       => 'TEXT',
            'help_text'  => 'The widget data will be posted to this url.',
            'disabled'   => true
        ),
   );

    /* The settings to setup in the setup-wizard. */
    private static $APICriteriaFields = array('url');

    /* -- Choice functions --
    public function resolution() {
        $hourly = array('hours' => 'Hourly');
        return array_merge($hourly, parent::resolution());
    }*/

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields()
    {
        return array(
            'Chart settings' => static::$histogramSettings,
            'API settings'   => static::$APISettings
        );
    }

    /**
     * getSetupFields
     * Returns the SetupFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getSetupFields() {
       return array_merge(parent::getSetupFields(), self::$APICriteriaFields);
    }

    /**
     * getCriteriaFields
     * Returns the CriteriaFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getCriteriaFields() {
       return array_merge(parent::getCriteriaFields(), self::$APICriteriaFields);
    }

    /**
     * layoutSetup
     * Set up the widget based on the layout.
     * --------------------------------------------------
     * @param layout
     * @return array
     * --------------------------------------------------
    */
    protected function layoutSetup($layout)
    {
        switch ($layout) {
        case SiteConstants::LAYOUT_MULTI_LINE:
            $this->setDiff(true);
            break;
        case SiteConstants::LAYOUT_COMBINED_BAR_LINE:
            $this->setDiff(false);
            $this->setSingle(true);
            break;
        case SiteConstants::LAYOUT_TABLE:
            $this->setDiff(false);
            $this->setSingle(true);
            break;
        default: break;
        }

        $this->setActiveHistogram($this->buildHistogramEntries());
    }

    /**
     * buildHistogramEntries
     * Build the histogram data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    private function buildHistogramEntries() 
    {
        /* Setting active histogram. */
        if ($this->toSingle) {
            /* Transforming to single. */
            return $this->transformToSingle($this->data['api']['data']);
        } else {
            /* Multi layout. */
            return $this->data['api'];
        }
    }

    /**
     * getWidgetApiUrl
     * Returns the POST url for the widget
     * --------------------------------------------------
     * @return (string) ($url) The url
     * --------------------------------------------------
     */
     public function getWidgetApiUrl() {
        return route('api.post',
                    array(SiteConstants::getLatestApiVersion(),
                          $this->user()->settings->api_key,
                          $this->id));
     }

    /**
     * saveSettings
     * Override to save the URL
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
     */
     public function saveSettings(array $inputSettings, $commit=true) {
         $inputSettings['url'] = $this->getWidgetApiUrl();
         return parent::saveSettings($inputSettings, $commit);
    }

}
?>
