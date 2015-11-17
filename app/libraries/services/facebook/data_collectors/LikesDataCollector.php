<?php
class LikesDataCollector extends HistogramDataCollector
{
    use FBHistogramDataCollectorTrait;
    protected static $insight = 'page_fans';
    protected static $period  = 'lifetime';
}
?>
