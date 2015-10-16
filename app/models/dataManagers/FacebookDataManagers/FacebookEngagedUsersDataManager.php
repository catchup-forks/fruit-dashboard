<?php

class FacebookEngagedUsersDataManager extends HistogramDataManager
{
    use FacebookHistogramDataManagerTrait;
    protected static $insight = 'page_engaged_users';
    protected static $period  = 'day';
}
?>
