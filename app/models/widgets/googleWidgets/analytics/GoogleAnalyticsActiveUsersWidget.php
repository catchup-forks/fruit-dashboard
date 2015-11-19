<?php

class GoogleAnalyticsActiveUsersWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramChartLayoutTrait;

    /* Data selector. */
    protected static $dataTypes = array('active_users');

    /* Data attribute. */
    protected static $isCumulative = false;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            SiteConstants::LAYOUT_MULTI_LINE => 'User count by sources',
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_MULTI_LINE => 'getChartData',
    );

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
        $this->setActiveHistogram($this->data['active_users']);
    }
}
?>
