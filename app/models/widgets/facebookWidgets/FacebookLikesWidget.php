<?php

class FacebookLikesWidget extends DataWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('likes');

    /* Data format definer. */
    use NumericWidgetTrait;

    /* Histogram layout data handler.  */
    use HistogramWidgetTrait;

    /* Chart data transformer. */
    use ChartWidgetTrait;
    
    /* Service settings. */
    use FacebookWidgetTrait;

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
            'Chart settings'    => static::$chartSettings,
            'Facebook settings' => static::$pageSettings
        );
    }

    /**
     * buildChartData
     * Build the chart data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function buildChartData()
    {
        /* Building the histogram. */
        $this->setActiveHistogram($this->data['likes']);
    }
}
?>
