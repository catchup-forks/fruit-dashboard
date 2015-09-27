<?php

class GoogleAnalyticsAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'google_analytics_bounce_rate'          => '{"col":1,"row":1,"size_x":4,"size_y":4}',
        'google_analytics_sessions'             => '{"col":5,"row":1,"size_x":4,"size_y":4}',
        'google_analytics_avg_session_duration' => '{"col":9,"row":1,"size_x":4,"size_y":4}',
        'google_analytics_top_sources' => '{"col":1,"row":5,"size_x":4,"size_y":4}',
    );
    protected static $service = 'google_analytics';
    /* /LATE STATIC BINDING. */
}