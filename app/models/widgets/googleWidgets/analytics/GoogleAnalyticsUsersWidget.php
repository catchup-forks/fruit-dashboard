<?php
class GoogleAnalyticsUsersWidget extends HistogramWidget implements iServiceWidget
{
    use GoogleAnalyticsWidgetTrait;
    use TransformableMultipleHistogramWidgetTrait;
    protected static $cumulative = TRUE;

}
?>
