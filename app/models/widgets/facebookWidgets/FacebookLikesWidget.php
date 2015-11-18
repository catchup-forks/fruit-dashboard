<?php

class FacebookLikesWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use FacebookWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;

    /* Data selector. */
    protected static $dataTypes = array('likes');

    /* Histogram data representation. */
    use HistogramTableLayoutTrait;
    use HistogramCountLayoutTrait;
    use HistogramChartLayoutTrait;

    /* Data attribute. */
    protected static $isCumulative = true;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            'combined-bar-line'  => 'Chart',
            'table'              => 'Table',
            'count'              => 'Count'
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        'combined-bar-line' => 'getChartData',
        'table'             => 'getTableData',
        'count'             => 'getCountData',
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
        return $this->data['likes'];
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
        return 'The number of likes on your page ' . $this->getPage()->name;
    }
}
?>
