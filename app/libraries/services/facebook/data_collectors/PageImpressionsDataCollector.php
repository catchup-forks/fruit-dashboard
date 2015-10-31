<?php

class PageImpressionsDataCollector extends HistogramDataCollector  
{
    use FBHistogramDataCollectorTrait;
    protected static $insight = 'page_impressions_unique';
    protected static $period  = 'day';
}
?>
