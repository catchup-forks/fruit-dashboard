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
    private $connection;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new TwitterConnector($user);
        $this->connection = $connector->connect();
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
     * ================================================== *
     *                  PROTECTED SECTION                 *
     * ================================================== *
    */

    /**
     * getUserData
     * --------------------------------------------------
     * Getting the User's data from the connection.
     * @return object
     * @throws TwitterNotConnected
     * --------------------------------------------------
    */
    protected function getUserData() {
        return $this->connection->get("account/verify_credentials");
    }

} /* TwitterDataColector */
