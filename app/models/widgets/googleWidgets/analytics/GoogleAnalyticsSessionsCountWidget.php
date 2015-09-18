<?php

class GoogleAnalyticsSessionsCountWidget extends CountWidget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    protected static $histogramDescriptor = 'google_analytics_sessions';
}
?>