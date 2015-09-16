<?php

class GoogleAnalyticsAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'google_analytics_bounce_rate'          => '{"col":1,"row":1,"size_x":6,"size_y":6}',
        'google_analytics_sessions'             => '{"col":7,"row":1,"size_x":6,"size_y":6}',
        'google_analytics_avg_session_duration' => '{"col":1,"row":7,"size_x":6,"size_y":6}',
    );
    protected static $service = 'google_analytics';
    /* /LATE STATIC BINDING. */
}