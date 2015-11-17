<?php
class GoogleAnalyticsSessionsWidget extends MultipleHistogramWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('users', 'new_users');

    /* Data attribute. */
    protected static $isCumulative = true;

    /* Service settings. */
    use GoogleAnalyticsWidgetTrait;

    /* Histogram data representation. */
    use MultipleHistogramWidgetTrait;

    /**
     * buildHeader
     * Building the table header.
     * --------------------------------------------------
     * @return null 
     * --------------------------------------------------
     */
    protected function buildHeader()
    {
        $this->addCol('Time');
        $this->addCol('Users');
        $this->addCol('New users');
        $this->addCol('Retention');
        $this->addCol('Churn');
    }

    /**
     * buildContent
     * Build the chart data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    protected function buildContent()
    {
        $this->setLength(5);
        $this->setResolution('days');
        $this->setSingle(true);

        $this->setActiveHistogram($this->transformToSingle($this->data['users']['data']));
        var_dump($this->buildHistogram());

        $this->setActiveHistogram($this->transformToSingle($this->data['new_users']['data']));
        dd($this->buildHistogram());

        return $this->data['sessions'];
        $this->setSingle(true);
        /* Building the histogram. */
        return $this->transformToSingle($this->data['sessions']['data']);
    }
}
?>
