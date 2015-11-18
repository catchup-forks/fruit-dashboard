<?php

class GoogleAnalyticsUsersWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramChartLayoutTrait;
    use HistogramTableLayoutTrait;
    use HistogramCountLayoutTrait;

    /* Data selector. */
    protected static $dataTypes = array('users');

    /* Data attribute. */
    protected static $isCumulative = true;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            SiteConstants::LAYOUT_MULTI_LINE        => 'User count by sources',
            SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'Sum users and difference by time',
            SiteConstants::LAYOUT_TABLE             => 'Table layout',
            SiteConstants::LAYOUT_COUNT             => 'Sum user count'
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        SiteConstants::LAYOUT_MULTI_LINE        => 'getChartData',
        SiteConstants::LAYOUT_COMBINED_BAR_LINE => 'getChartData',
        SiteConstants::LAYOUT_TABLE             => 'getTableData',
        SiteConstants::LAYOUT_COUNT             => 'getCountData',
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
        switch ($layout) {
        case SiteConstants::LAYOUT_MULTI_LINE:
            $this->setDiff(true);
            break;
        case SiteConstants::LAYOUT_COMBINED_BAR_LINE:
            $this->setDiff(false);
            $this->setSingle(true);
            break;
        case SiteConstants::LAYOUT_TABLE:
            $this->setDiff(false);
            $this->setSingle(true);
            break;
        case SiteConstants::LAYOUT_COUNT:
            $this->setDiff(false);
            $this->setSingle(true);
            break;
        default: break;
        }

        $this->setActiveHistogram($this->buildHistogramEntries());
    }

    /**
     * buildHistogramEntries
     * Build the histogram data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    private function buildHistogramEntries() 
    {
        /* Setting active histogram. */
        if ($this->toSingle) {
            /* Transforming to single. */
            return $this->transformToSingle($this->data['users']['data']);
        } else {
            /* Multi layout. */
            return $this->data['users'];
        }
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
        return 'The number of users on your property ' . $this->getProperty()->name;
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
        return $this->getProperty()->name;
    }
}
?>
