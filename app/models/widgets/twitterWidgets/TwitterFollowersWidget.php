<?php

class TwitterFollowersWidget extends HistogramWidget implements iServiceWidget
{
    use TwitterWidgetTrait;
    protected static $cumulative = TRUE;
}
?>
