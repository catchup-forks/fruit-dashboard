<?php
class GoogleAnalyticsSessionsPerUserWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;

    /* Histogram data representation. */
    use HistogramChartLayoutTrait;
    use HistogramTableLayoutTrait;

    /* Overriding default format. */
    protected static $format = '%.2f';

    /* Data selector. */
    protected static $dataTypes = array('users', 'sessions');

    /* Data attribute. */
    protected static $isCumulative = false;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            SiteConstants::LAYOUT_SINGLE_LINE => 'Single line chart ',
            SiteConstants::LAYOUT_TABLE       => 'Table layout',
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_SINGLE_LINE => 'getChartData',
        SiteConstants::LAYOUT_TABLE       => 'getTableData',
    );

    /**
     * layoutSetup
     * Set up the widget based on the layout.
     * --------------------------------------------------
     * @param string layout
     * @return array
     * --------------------------------------------------
    */
    protected function layoutSetup($layout)
    {
        $this->setActiveHistogram($this->createDataSet());
    }

    /**
     * createDataSet
     * Calculating the values for the metric.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    private function createDataSet()
    {
        return array(array('timestamp' => 123434112, 'value' => 123));
    }
}
?>
