<?php
class GoogleAnalyticsGoalCompletionWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use GoogleAnalyticsGoalWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramChartLayoutTrait;
    use HistogramTableLayoutTrait;
    use HistogramCountLayoutTrait;

    /* Data selector. */
    protected static $dataTypes = array('goal_completion');

    /* Data attribute. */
    protected static $isCumulative = true;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            'multi-line'        => 'Goal completion count by sources',
            'combined-bar-line' => 'Sum goal completions and difference by time',
            'table'             => 'Table layout',
            'count'             => 'Sum goal completion count'
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
                $this->setDiff(false);
                $this->setSingle(true);
            break;
            case 'table':
                $this->setDiff(false);
                $this->setSingle(true);
            break;
            case 'count':
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
            return $this->transformToSingle($this->data['goal_completion']['data']);
        } else {
            /* Multi layout. */
            return $this->data['goal_completion'];
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
        return 'The number of copmletions of ' . $this->getGoal()->name. ' on your property'  . $this->getProperty()->name;
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
