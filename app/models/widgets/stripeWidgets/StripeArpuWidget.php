<?php

class StripeArpuWidget extends HistogramWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('arpu');

    /* Data attribute. */
    protected static $isCumulative = false;

    /* Service settings. */
    use StripeWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;
    use HistogramTableLayoutTrait;
    use HistogramChartLayoutTrait;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'Chart',
            SiteConstants::LAYOUT_TABLE             => 'Table',
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'getChartData',
        SiteConstants::LAYOUT_TABLE             => 'getTableData',
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
        $this->setActiveHistogram($this->data['arpu']);
    }

}
?>
