<?php

class FacebookLikesCountWidget extends CountWidget implements iServiceWidget
{
    use FacebookWidgetTrait;
    protected static $histogramDescriptor = 'facebook_likes';
}
?>