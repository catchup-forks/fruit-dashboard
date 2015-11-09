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
    use HistogramTableLayoutTrait;
    use HistogramCountLayoutTrait;
    use HistogramChartLayoutTrait;
    use HistogramWidgetTrait;

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            'chart'  => 'Chart',
            'table'  => 'Table',
            'count'  => 'Count'
        );
    }

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
}
?>
