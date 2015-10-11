<?php

class FacebookAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    /* LATE STATIC BINDING. */
    protected static $widgets = array(
        'facebook_likes' => array(
            'settings' => array('type' => 'chart')
        ),
        'facebook_page_impressions' => array(),
        'facebook_engaged_users'    => array()
    );
    protected static $service = 'facebook';
    /* /LATE STATIC BINDING. */
}