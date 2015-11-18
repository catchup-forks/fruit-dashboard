<?php

class FacebookPageImpressionsWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use FacebookWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;

    /* Histogram data representation. */
    use HistogramTableLayoutTrait;
    use HistogramChartLayoutTrait;
    use HistogramCountLayoutTrait;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'Chart',
            SiteConstants::LAYOUT_TABLE             => 'Table',
            SiteConstants::LAYOUT_COUNT             => 'Sum page impressions'
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'getChartData',
        SiteConstants::LAYOUT_TABLE             => 'getTableData',
        SiteConstants::LAYOUT_COUNT             => 'getCountData'
    );

    /* Data selector. */
    protected static $dataTypes = array('page_impressions');

    /* Data attribute. */
    protected static $isCumulative = true;

    /**
     * buildHistogramEntries
     * Build the histogram data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    protected function buildHistogramEntries() 
    {
        return $this->data['page_impressions'];
    }

    /**
     * getCountDescription
     * --------------------------------------------------
     * Return the description for the count widget.
     * @return array
     * --------------------------------------------------
     */
    protected function getCountDescription()
    {
        return 'The number of page impressions on ' . $this->getPage()->name;
    }
}
?>
