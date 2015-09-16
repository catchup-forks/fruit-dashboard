<?php

class GoogleAnalyticsSessionsCountWidget extends CountWidget
{
    use GoogleAnalyticsWidgetTrait;
    protected static $histogramDescriptor = 'google_analytics_sessions';
}
?>