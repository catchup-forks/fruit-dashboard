<?php

use Facebook\Facebook;
use Facebook\PersistentData\PersistentDataInterface;

/**
* --------------------------------------------------------------------------
* FacebookConnector:
*       Wrapper functions for Facebook connection
* Usage:
*       $connector->connect();
* --------------------------------------------------------------------------
*/

class LaravelFacebookSessionPersistendDataHandler implements PersistentDataInterface {
    /**
     * @var string Prefix to use for session variables.
     */
    protected $sessionPrefix = 'FBRLH_';

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return Session::get($this->sessionPrefix . $key);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {

        Session::put($this->sessionPrefix . $key, $value);
    }
}

class FacebookConnector extends GeneralServiceConnector
{
    protected static $service     = 'facebook';
    protected static $permissions = array('email', 'user_likes');

    protected $fb;

    /* -- Constructor -- */
    function __construct($user) {
        parent::__construct($user);
        $persistentDataHandler = new LaravelFacebookSessionPersistendDataHandler();
        // FIXME graph version -> ENV
        $this->fb = new Facebook(array(
            'app_id'                  => $_ENV['FACEBOOK_APP_ID'],
            'app_secret'              => $_ENV['FACEBOOK_APP_SECRET'],
            'default_graph_version'   => 'v2.2',
            'persistent_data_handler' => $persistentDataHandler
        ));
    }

    /**
     * getFacebookConnectUrl
     * --------------------------------------------------
     * Returns the twitter connect url, based on config.
     * @return array
     * --------------------------------------------------
     */
    public function getFacebookConnectUrl() {
        $helper = $this->fb->getRedirectLoginHelper();
        return $helper->getLoginUrl(route('service.facebook.connect', static::$permissions));
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
        $helper = $this->fb->getRedirectLoginHelper();
        $this->createConnection($helper->getAccessToken(), '');
    }


} /* TwitterConnector */
