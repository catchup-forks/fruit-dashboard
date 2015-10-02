<?php
class GoogleAnalyticsAvgSessionDurationWidget extends HistogramWidget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    protected static $format = '%d s';
}
?>