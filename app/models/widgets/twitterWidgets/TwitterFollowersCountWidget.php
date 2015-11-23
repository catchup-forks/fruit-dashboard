<?php

class TwitterFollowersCountWidget extends Widget implements iServiceWidget
{
    use TwitterWidgetTrait;
    protected static $histogramDescriptor = 'twitter_followers';
}
?>
