<?php
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

/**
* --------------------------------------------------------------------------
* FacebookConnector:
*       Wrapper functions for Facebook connection
* Usage:
*       // For the connection url, and tokens.
*       TwitterHelper::getTwitterConnectURL()
*
*       // For connecting the user
*       $twitterConnector = new FacebookConnector($user);
*       $twitterConnector->getTokens($token_ours, $token_request, $token_secret, $verifier);
*       $connector->connect();
* --------------------------------------------------------------------------
*/

class FacebookConnector extends GeneralServiceConnector
{
    protected static $service = 'facebook';

    /* -- Constructor -- */
    function __construct($user) {
        parent::__construct($user);
        FacebookSession::setDefaultApplication($_ENV['FACEBOOK_APP_ID'], $_ENV['FACEBOOK_APP_SECRET']);

    }

    /**
     * getFacebookConnectURL
     * --------------------------------------------------
     * Returns the twitter connect url, based on config.
     * @return array
     * --------------------------------------------------
     */
    public function getFacebookConnectUrl() {
        $loginUrlGenerator = new FacebookRedirectLoginHelper(route('service.facebook.connect'));
        return $loginUrlGenerator->getLoginUrl();
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getTokens
     * Retrieving the access tokens, OAUTH.
     * --------------------------------------------------
     * @return None
     * --------------------------------------------------
     */
    public function getTokens() {

        $helper = new FacebookRedirectLoginHelper();
        $session = $helper->getSessionFromRedirect();
        Log::info($session);
    }


} /* TwitterConnector */
