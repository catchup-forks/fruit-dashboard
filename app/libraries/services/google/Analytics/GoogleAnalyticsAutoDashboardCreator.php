<?php

class GoogleAnalyticsAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $widgets = array(
        'google_analytics_bounce_rate'          => array(),
        'google_analytics_sessions'             => array(),
        'google_analytics_avg_session_duration' => array(),
        'google_analytics_top_sources'          => array(),
    );
    protected static $service = 'google_analytics';
    /* /LATE STATIC BINDING. */
}