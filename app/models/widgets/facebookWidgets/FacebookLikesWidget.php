<?php

class FacebookLikesWidget extends HistogramWidget implements iServiceWidget
{
    /* Data selector. */
    protected static $dataTypes = array('likes');

    /* Data attribute. */
    protected static $isCumulative = true;

    /* Service settings. */
    use FacebookWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;

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
}
?>
