<?php

class PageImpressionsDataManager extends HistogramDataCollector  
{
    use FBHistogramDataCollectorTrait;
    protected static $insight = 'page_impressions_unique';
    protected static $period  = 'day';
}
?>
