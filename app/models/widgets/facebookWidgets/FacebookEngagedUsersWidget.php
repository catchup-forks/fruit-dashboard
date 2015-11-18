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

    /* -- Choice functions -- */
    public function type()
    {
        return array(
            'single-line' => 'Chart',
            'table'       => 'Table',
        );
    }

    /* The layout function map. */
    protected static $functionMap = array(
        'single-line' => 'getChartData',
        'table'       => 'getTableData',
    );

    /* Data selector. */
    protected static $dataTypes = array('engaged_users');

    /* Data attribute. */
    protected static $isCumulative = false;

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
}
?>
