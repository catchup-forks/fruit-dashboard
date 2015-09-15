<?php

class FacebookAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'facebook_likes'            => '{"col":4,"row":1,"size_x":6,"size_y":6}',
        'facebook_new_likes'        => '{"col":2,"row":7,"size_x":5,"size_y":5}',
        'facebook_page_impressions' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'facebook';
    /* /LATE STATIC BINDING. */
}