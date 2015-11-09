<?php
class GoogleAnalyticsSessionsWidget extends HistogramWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('sessions');

    /* Data attribute. */
    protected static $isCumulative = true;

    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    /**
     * buildChartData
     * Build the chart data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    protected function buildChartData()
    {
        $this->setSingle(true);

        /* Building the histogram. */
        return $this->transformToSingle($this->data['sessions']['data']);
    }
}
?>
