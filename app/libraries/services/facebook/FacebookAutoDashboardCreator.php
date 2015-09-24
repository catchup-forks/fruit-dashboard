<?php

class FacebookAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'facebook_likes'            => '{"col":1,"row":1,"size_x":4,"size_y":4}',
        'facebook_new_likes'        => '{"col":5,"row":1,"size_x":4,"size_y":4}',
        'facebook_page_impressions' => '{"col":9,"row":1,"size_x":4,"size_y":4}',
        'facebook_engaged_users'    => '{"col":1,"row":5,"size_x":4,"size_y":4}',
    );
    protected static $service = 'facebook';
    /* /LATE STATIC BINDING. */
}