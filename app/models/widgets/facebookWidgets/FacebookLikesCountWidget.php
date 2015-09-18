<?php

class FacebookLikesCountWidget extends CountWidget
{
    use FacebookWidgetTrait;
    protected static $histogramDescriptor = 'facebook_likes';
}
?>