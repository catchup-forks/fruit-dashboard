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

/* Overriding session handling in default facebook login helper */
class LaravelFacebookRedirectLoginHelper extends FacebookRedirectLoginHelper {
    protected function storeState($state)
    {
        Session::put('facebook.state', $state);
    }

    protected function loadState()
    {
        return $this->state = Session::get('facebook.state');
    }
}

class FacebookConnector extends GeneralServiceConnector
{
    protected static $service = 'facebook';

    protected $helper;

    /* -- Constructor -- */
    function __construct($user) {
        parent::__construct($user);
        FacebookSession::setDefaultApplication($_ENV['FACEBOOK_APP_ID'], $_ENV['FACEBOOK_APP_SECRET']);

        $this->helper = new LaravelFacebookRedirectLoginHelper(route('service.facebook.connect'));
    }

    /**
     * getFacebookConnectUrl
     * --------------------------------------------------
     * Returns the twitter connect url, based on config.
     * @return array
     * --------------------------------------------------
     */
    public function getFacebookConnectUrl() {
        return $this->helper->getLoginUrl();
    }

    /**
     * connect
     * --------------------------------------------------
     * @return FacebookSession
     * --------------------------------------------------
     */
    public function connect() {
        $session = new FacebookSession($this->getConnection()->access_token);
        return $session;
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
        $session = $this->helper->getSessionFromRedirect();
        $this->createConnection($session->getAccessToken(), '');
    }


} /* TwitterConnector */
