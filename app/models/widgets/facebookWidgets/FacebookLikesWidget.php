<?php

class FacebookLikesWidget extends HistogramWidget implements iServiceWidget
{
    use FacebookWidgetTrait;
    protected static $cumulative = TRUE;
}
?>
