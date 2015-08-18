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
    private $user;
    private $session;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $connector = new FacebookConnector($user);
        $this->session = $connector->connect();
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
        try {
            $profile = (new FacebookRequest(
                $this->session, 'GET', '/me'
            ))->execute()->getGraphObject(GraphUser::className());
            Log::info($profile->getName());
        } catch (FacebookRequestException $e) {
            Log::info($e->getMessage());
        }

        $request = new FacebookRequest(
            $this->session, 'GET',
            '/' . 927404167302370 . '/insights/' . 'page_impressions',
            array (
                'period' => 'month'
            )
        );
        $response = $request->execute();
        $graphObject = $response->getGraphObject();
        var_dump($graphObject);

        //return $userData->followers_count;
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
