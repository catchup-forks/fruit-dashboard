<?php

/**
* --------------------------------------------------------------------------
* TwitterDataCollector:
*       Getting data from twitter account.
* --------------------------------------------------------------------------
*/

class TwitterDataCollector
{
    /* -- Class properties -- */
    private $user;
    private $twitter;

    /* -- Constructor -- */
    function __construct($user, $connector=null) {
        $this->user = $user;
        if (is_null($connector)) {
            $connector = new TwitterConnector($user);
        }
        $this->twitter = $connector->connect();
    }

    /**
     * getFollowersCount
     * --------------------------------------------------
     * Getting the follower count from twitter.
     * @return int
     * @throws TwitterNotConnected
     * --------------------------------------------------
    */
    public function getFollowersCount() {
        $userData = $this->getUserData();
        return $userData->followers_count;
    }

    /**
     * getMentions
     * --------------------------------------------------
     * Getting the mentions count from twitter.
     * @param $count
     * @return stdObject
     * @throws TwitterNotConnected
     * --------------------------------------------------
    */
    public function getMentions($count) {
        return $this->twitterGet('statuses/mentions_timeline', array('count' => $count));
    }

    /**
     * ================================================== *
     *                  PROTECTED SECTION                 *
     * ================================================== *
    */

    /**
     * getUserData
     * --------------------------------------------------
     * Getting the User's data from twitter.
     * @return object
     * @throws TwitterNotConnected
     * --------------------------------------------------
    */
    public function getUserData() {
        return $this->twitterGet("account/verify_credentials");
    }

    /**
     * twitterGet
     * --------------------------------------------------
     * Sending get request to twitter.
     * @param string $url
     * @return object
     * @throws ServiceException
     * --------------------------------------------------
    */
    private function twitterGet($url, array $parameters=array()) {
        try {
            return $this->twitter->get($url, $parameters);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Twitter connection error.", 1);
        }

    }

} /* TwitterDataColector */
