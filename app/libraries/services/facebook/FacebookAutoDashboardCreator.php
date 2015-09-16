<?php

class FacebookAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'facebook_likes'            => '{"col":1,"row":1,"size_x":6,"size_y":6}',
        'facebook_new_likes'        => '{"col":1,"row":7,"size_x":6,"size_y":6}',
        'facebook_page_impressions' => '{"col":7,"row":1,"size_x":6,"size_y":6}',
    );
    protected static $service = 'facebook';
    /* /LATE STATIC BINDING. */
}