<?php

class WebhookHistogramWidget extends HistogramWidget
{
    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramChartLayoutTrait;
    use HistogramTableLayoutTrait;

    /* Data selector. */
    protected static $dataTypes = array('webhook');

    /* Data attribute. */
    protected static $isCumulative = true;

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
    private static $webhookSettings = array(
        'url' => array(
            'name'       => 'Webhook url',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The widget data will be populated from data on this url.'
        ),
    );

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
            'Chart settings'   => static::$histogramSettings,
            'Webhook settings' => static::$webhookSettings
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
        return array_merge(parent::getSetupFields(), array('url'));
     }

    /**
     * getCriteriaFields
     * Returns the CriteriaFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getCriteriaFields() {
        return array_merge(parent::getSetupFields(), array('url'));
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
            return $this->transformToSingle($this->data['webhook']['data']);
        } else {
            /* Multi layout. */
            return $this->data['webhook'];
        }
    }
}
?>
