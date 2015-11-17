<?php

class FacebookEngagedUsersWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use FacebookWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;

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
