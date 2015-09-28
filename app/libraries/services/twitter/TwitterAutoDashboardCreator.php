<?php

class TwitterAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */

    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'twitter_followers'       => '{"col":1,"row":1,"size_x":4,"size_y":4}',
        'twitter_followers_count' => '{"col":5,"row":1,"size_x":2,"size_y":4}',
        'twitter_mentions'        => '{"col":7,"row":1,"size_x":6,"size_y":8}',
    );
    protected static $service = 'twitter';
    /* /LATE STATIC BINDING. */

}