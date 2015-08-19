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
     * getUserID
     * --------------------------------------------------
     * Returns the user's id.
     * @return array
     * --------------------------------------------------
     */
    public function getUserID() {
        try {
            $response = $this->fb->get('/me/?fields=id', $this->accessToken);
        } catch (FacebookResponseException $e) {
            return null;
        } catch (FacebookSDKException $e) {
            return null;
        }
        return $response->getGraphUser()['id'];
    }

    /**
     * savePages
     * --------------------------------------------------
     * saves The user's pages
     * @return array
     * --------------------------------------------------
     */
    public function savePages() {
        $userId = $this->getUserID();
        if (is_null($userId)) {
            return;
        }
        try {
            $response = $this->fb->get('/' . $userId . '/accounts', $this->accessToken);
        } catch (FacebookResponseException $e) {
            return;
        } catch (FacebookSDKException $e) {
            return;
        }

        $pages = array();
        foreach ($response->getGraphEdge() as $graphNode) {
            $page = new FacebookPage(array(
                'page_id' => $graphNode['id'],
                'name'    => $graphNode['name']
            ));
            $page->user()->associate($this->user);
            $page->save();
            array_push($pages, $page);
        }
        return $pages;
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
        /*
        $request = new FacebookRequest(
            $this->app, $this->accessToken, 'GET',
            '/' . 927404167302370 . '/insights/' . 'page_impressions',
            array (
                'period' => 'month'
            )
        );*/

        $response = $this->fb->get('/927404167302370/insights/page_engaged_users?period=days_28', $this->accessToken);
        var_dump($response);

        //return $userData->followers_count;
    }


} /* FacebookDataCollector */
