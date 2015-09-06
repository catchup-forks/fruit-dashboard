<?php

class TwitterAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */

    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'twitter_followers'     => '{"col":2,"row":7,"size_x":5,"size_y":5}',
        'twitter_new_followers' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'twitter';
    /* /LATE STATIC BINDING. */

}