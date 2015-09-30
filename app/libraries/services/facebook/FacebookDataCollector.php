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
    function __construct($user, $connector=null) {
        $this->user = $user;
        if (is_null($connector)) {
            $connector = new FacebookConnector($user);
        }
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
        } catch (Exception $e) {
            throw new ServiceException("Error Processing Request", 1);
        }

        /* Deleting previous pages. */
        $this->user->facebookPages()->delete();
        $pages = array();
        foreach ($response->getGraphEdge() as $graphNode) {
            $page = new FacebookPage(array(
                'id'   => $graphNode['id'],
                'name' => $graphNode['name']
            ));
            $page->user()->associate($this->user);
            array_push($pages, $page);
        }

        if (count($pages) > 0) {
            /* Only refreshing if we have results. */
            $this->user->facebookPages()->delete();
            foreach ($pages as $page) {
                $page->save();
            }
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
     * getEngagedUsers
     * Getting the number of engaged users.
     * --------------------------------------------------
     * @param page
     * @return int
     * @throws FacebookNotConnected
     * --------------------------------------------------
    */
    public function getEngagedUsers($page) {
        $insightData = $this->getInsight('page_engaged_users', $page, array('period' => 'day'))[0];
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
     * @param int $pageId
     * @param string $insight
     * @param array $params
     * @return array
     * @throws FacebookNotConnected
     * --------------------------------------------------
    */
    public function getInsight($insight, $pageId, $params=array()) {
        $paramstr = '?';
        foreach ($params as $key=>$value) {
            $paramstr .= '&' . $key . '='. $value;
        }
        try {
            $response =  $this->fb->get('/' . $pageId . '/insights/' . $insight . $paramstr , $this->accessToken);
            return $response->getDecodedBody()['data'];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Facebook connection error.", 1);
        }
    }

} /* FacebookDataCollector */
