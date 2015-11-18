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
            'multi-line'        => 'User count by sources',
            'combined-bar-line' => 'Sum users and difference by time',
            'table'             => 'Table layout',
            'count'             => 'Sum user count'
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        'multi-line'        => 'getChartData',
        'combined-bar-line' => 'getChartData',
        'table'             => 'getTableData',
        'count'             => 'getCountData',
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
            case 'multi-line':
                $this->setDiff(true);
            break;
            case 'combined-bar-line':
                $this->setSingle(true);
            break;
            case 'table':
                $this->setSingle(true);
            break;
            case 'count':
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
     * __call
     * Map some functions that are needed to be called. 
     * --------------------------------------------------
     * @param string name
     * @param array args
     * @return mixed
     * --------------------------------------------------
    */
    public function __call($name, $args)
    {
        switch ($name) {
        case 'getCombinedChartData':
            return call_user_func_array(array(&$this, 'getChartData'), $args);
            break;
        }

        return parent::__call($name, $args);
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
