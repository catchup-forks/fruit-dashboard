<?php

class TwitterAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */

    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'twitter_followers'     => '{"col":1,"row":1,"size_x":6,"size_y":6}',
        'twitter_new_followers' => '{"col":1,"row":7,"size_x":6,"size_y":6}',
    );
    protected static $service = 'twitter';
    /* /LATE STATIC BINDING. */

}