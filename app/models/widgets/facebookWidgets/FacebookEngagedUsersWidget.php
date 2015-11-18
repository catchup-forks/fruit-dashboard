<?php

class FacebookEngagedUsersWidget extends HistogramWidget implements iServiceWidget
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
            SiteConstants::LAYOUT_COUNT             => 'Sum engaged users count'
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'getChartData',
        SiteConstants::LAYOUT_TABLE             => 'getTableData',
        SiteConstants::LAYOUT_COUNT             => 'getCountData'
    );

    /* Data selector. */
    protected static $dataTypes = array('engaged_users');

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
        return $this->data['engaged_users'];
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
        return 'The number of engaged users on your page ' . $this->getPage()->name;
    }
}
?>
