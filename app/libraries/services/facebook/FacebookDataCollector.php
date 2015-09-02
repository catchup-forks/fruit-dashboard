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
     * Returns the user's id.
     * --------------------------------------------------
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
     * Saves The user's pages
     * --------------------------------------------------
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

        /* Deleting previous pages. */
        $this->user->facebookPages()->delete();

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
     * Getting the total likes count from twitter.
     * --------------------------------------------------
     * @param page
     * @return int
     * @throws FacebookNotConnected
     * --------------------------------------------------
    */
    public function getTotalLikes($page) {
        $insightData = $this->getInsight('page_fans', $page)[0];
        return end($insightData['values'])['value'];
    }

    /**
     * getPageImpressions
     * Getting the number of page impressions.
     * --------------------------------------------------
     * @param page
     * @return int
     * @throws FacebookNotConnected
     * --------------------------------------------------
    */
    public function getPageImpressions($page) {
        $insightData = $this->getInsight('page_impressions_unique', $page)[0];
        return end($insightData['values'])['value'];
    }

    /**
     * getInsight
     * Getting the specific insight.
     * --------------------------------------------------
     * @param FacebookPage $page
     * @param string $insight
     * @param array $params
     * @return array
     * @throws FacebookNotConnected
     * --------------------------------------------------
    */
    public function getInsight($insight, $page, $params=array()) {
        $paramstr = '?';
        foreach ($params as $key=>$value) {
            $paramstr .= '&' . $key . '='. $value;
        }
        $response =  $this->fb->get('/' . $page->page_id . '/insights/' . $insight . $paramstr , $this->accessToken);
        var_dump($response->getDecodedBody());
        return $response->getDecodedBody()['data'];
    }

} /* FacebookDataCollector */
