<?php

class TwitterFollowersWidget extends HistogramWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('followers');

    /* Data attribute. */
    protected static $isCumulative = true;

    /* Service settings. */
    use TwitterWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;
    use HistogramTableLayoutTrait;
    use HistogramCountLayoutTrait;
    use HistogramChartLayoutTrait;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'Chart',
            SiteConstants::LAYOUT_TABLE             => 'Table',
            SiteConstants::LAYOUT_COUNT             => 'Sum followers'
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'getChartData',
        SiteConstants::LAYOUT_TABLE             => 'getTableData',
        SiteConstants::LAYOUT_COUNT             => 'getCountData'
    );


    /**
     * buildHistogramEntries
     * Build the histogram data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    protected function buildHistogramEntries() 
    {
        return $this->data['followers'];
    }

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
        $this->setActiveHistogram($this->buildHistogramEntries());
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
        return 'The number of followers on your twitter user ' . $this->getUser();
    }

    /**
     * getCountFooter
     * --------------------------------------------------
     * Return the footer for the count widget.
     * @return array
     * --------------------------------------------------
     */
    protected function getCountFooter()
    {
        return $this->getUser();
    }
}
?>
