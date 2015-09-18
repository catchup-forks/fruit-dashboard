<?php

class TwitterFollowersCountWidget extends CountWidget implements iServiceWidget
{
    use TwitterWidgetTrait;
    protected static $histogramDescriptor = 'twitter_followers';
}
?>
