<?php

class TwitterAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* -- Class properties -- */

    /* LATE STATIC BINDING. */
    protected static $widgets = array(
        'twitter_followers'       => array(),
        'twitter_followers_count' => array(),
        'twitter_mentions'        => array()
    );
    protected static $service = 'twitter';
    /* /LATE STATIC BINDING. */

}