<?php
class GoogleAnalyticsSessionsWidget extends HistogramWidget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    protected static $cumulative = TRUE;
}
?>