<?php

class FacebookLikesDataManager extends HistogramDataManager
{
    use FacebookHistogramDataManagerTrait;
    protected static $insight = 'page_fans';
    protected static $period  = 'lifetime';
}
?>
