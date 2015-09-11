<?php

class GoogleAnalyticsAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'google_analytics_bounce_rate' => '{"col":4,"row":1,"size_x":6,"size_y":6}',
        'google_analytics_sessions' => '{"col":2,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'google_analytics';
    /* /LATE STATIC BINDING. */
}