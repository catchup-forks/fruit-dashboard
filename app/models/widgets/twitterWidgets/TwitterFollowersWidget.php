<?php

class TwitterFollowersWidget extends HistogramWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('followers');

    /* Data attribute. */
    protected static $isCumulative = true;

    /* Service settings. */
    use TwitterWidgetTrait;

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
        return $this->data['followers'];
    }
}
?>
