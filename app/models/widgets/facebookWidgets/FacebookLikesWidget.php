<?php

class FacebookLikesWidget extends HistogramWidget implements iServiceWidget
{
    /* Service settings. */
    use FacebookWidgetTrait;

    /* Histogram data representation. */
    use HistogramWidgetTrait;

    /* Data selector. */
    protected static $dataTypes = array('likes');

    /* Data attribute. */
    protected static $isCumulative = true;

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
