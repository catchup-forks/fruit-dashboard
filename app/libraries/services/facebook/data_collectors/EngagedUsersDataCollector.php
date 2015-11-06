<?php

class EngagedUsersDataCollector extends HistogramDataCollector  
{
    use FBHistogramDataCollectorTrait;
    protected static $insight = 'page_engaged_users';
    protected static $period  = 'day';
}
?>
