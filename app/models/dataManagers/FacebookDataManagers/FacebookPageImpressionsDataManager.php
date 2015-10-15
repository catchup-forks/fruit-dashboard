<?php

class FacebookPageImpressionsDataManager extends HistogramDataManager
{
    use FacebookHistogramDataManagerTrait;
    protected static $insight = 'page_impressions_unique';
    protected static $period  = 'day';
}
?>
