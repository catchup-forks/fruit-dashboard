<?php

class GoogleAnalyticsUsersChartWidget extends DataWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('users');

    /* Data format definer. */
    use NumericWidgetTrait;

    /* Histogram layout data handler.  */
    use HistogramWidgetTrait;

    /* Chart data transformer. */
    use MultipleChartWidgetTrait;
    
    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

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
            'Chart settings'            => static::$chartSettings,
            'Google Analytics Settings' => static::$profileSettings
        );
    }

    /**
     * getTemplateData
     * Return all values used in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateData()
    {
        return array_merge(parent::getTemplateData(), array(
            'data'          => $this->buildChartData(),
            'currentDiff'   => array(0),
            'currentValue'  => array(0),
            'format'        => $this->getFormat(),
        ));
    }

    /**
     * buildChartData
     * Build the chart data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    private function buildChartData()
    {
        /* Building the histograms. */
        $this->setActiveHistogram($this->transformToSingle($this->data['new_users']['data']));
        return $this->getChartJSData('Y-m-d', TRUE);
    }
}
?>
