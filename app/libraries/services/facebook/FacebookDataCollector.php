<?php

use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\GraphUser;
/**
* --------------------------------------------------------------------------
* FacebookDataCollector:
*       Getting data from facebook account.
* --------------------------------------------------------------------------
*/

class FacebookDataCollector
{
    /* -- Class properties -- */
    /**
     * @var User The user entity for this collector.
     *
     */
    private $user;

    /**
     * @var string access token
     *
     */
    private $accessToken;

    /**
     * @var Facebook The Facebook entity.
     *
     */
    private $fb;

    /**
     * @var FacebookApp The Facebook app entity.
     *
     */
    private $app;


    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new FacebookConnector($user);
        $this->accessToken = $connector->connect();
        $this->fb = $connector->getFB();
        $this->app = $this->fb->getApp();
    }

    /**
     * getTotalLikes
     * --------------------------------------------------
     * Getting the total likes count from twitter.
     * @return int
     * @throws FacebookNotConnected
     * --------------------------------------------------
    */
    public function getTotalLikes() {

        $request = new FacebookRequest(
            $this->app, $this->accessToken, 'GET',
            '/' . 927404167302370 . '/insights/' . 'page_impressions',
            array (
                'period' => 'month'
            )
        );
        $response = $this->fb->get($request->getUrl());
        var_dump($response);

        //return $userData->followers_count;
    }


} /* FacebookDataCollector */
