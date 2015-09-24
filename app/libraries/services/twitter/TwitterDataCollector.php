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
    private $connector;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new TwitterConnector($user);
        $this->connector = $connector->connect();
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
     * @return int
     * @throws TwitterNotConnected
     * --------------------------------------------------
    */
    public function getMentions() {
        return $this->connector->get('statuses/mentions_timeline');
    }

    /**
     * ================================================== *
     *                  PROTECTED SECTION                 *
     * ================================================== *
    */

    /**
     * getUserData
     * --------------------------------------------------
     * Getting the User's data from the connector.
     * @return object
     * @throws TwitterNotConnected
     * --------------------------------------------------
    */
    protected function getUserData() {
        return $this->connector->get("account/verify_credentials");
    }

} /* TwitterDataColector */
