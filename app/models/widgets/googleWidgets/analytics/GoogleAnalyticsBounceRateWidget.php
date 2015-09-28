<?php
class GoogleAnalyticsBounceRateWidget extends HistogramWidget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    protected static $format = '%.2f%%';
}
?>