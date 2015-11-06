<?php

class FacebookLikesWidget extends HistogramWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('likes');

    /* Data attribute. */
    protected static $isCumulative = TRUE;

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
        return array_merge(parent::getSettingsFields(), array(
            'Facebook settings' => static::$pageSettings
        ));
    }

    /**
     * buildChartData
     * Build the chart data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    protected function buildChartData()
    {
        /* Building the histogram. */
        return $this->data['likes'];
    }
}
?>
