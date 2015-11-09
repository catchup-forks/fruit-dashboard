<?php

class GoogleAnalyticsUsersWidget extends DataWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('users');

    /* Data format definer. */
    use NumericWidgetTrait;

    /* Histogram layout data handler.  */
    use MultipleHistogramWidgetTrait;

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
     * buildChartData
     * Build the chart data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    private function buildChartData()
    {
        /* Building the histogram. */
        $this->setActiveHistogram(
            $this->transformToSingle($this->data['users']['data'])
        );

        return $this->getChartJSData('Y-m-d', true);
    }
}
?>
