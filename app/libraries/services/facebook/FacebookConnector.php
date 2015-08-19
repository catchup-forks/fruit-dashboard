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
    public function get($key) {
        return Session::get($this->sessionPrefix . $key);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value) {
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
            'default_graph_version'   => $_ENV['FACEBOOK_DEFAULT_GRAPH_VERSION'],
            'persistent_data_handler' => $persistentDataHandler
        ));
    }

    /**
     * getFB
     * --------------------------------------------------
     * Returns the facebook entity.
     * @return Facebook
     * --------------------------------------------------
     */
    public function getFB() {
        return $this->fb;
    }

    /**
     * getFacebookConnectUrl
     * --------------------------------------------------
     * Returns the facebook connect url, based on config.
     * @return string
     * --------------------------------------------------
     */
    public function getFacebookConnectUrl() {
        $helper = $this->fb->getRedirectLoginHelper();
        if (App::environment('local')) {
            // we must use this in development
            return $helper->getLoginUrl('http://localhost:8001/service/facebook/connect', static::$permissions);
        }

        return $helper->getLoginUrl(route('service.facebook.connect'), static::$permissions);
    }

    /**
     * connect
     * --------------------------------------------------
     * @return FacebookSession
     * --------------------------------------------------
     */
    public function connect() {
        return $this->getConnection()->access_token;
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

        if (App::environment('local')) {
            $this->createConnection($helper->getAccessToken(str_replace(8000, 8001, URL::full())), '');
        } else {
            $this->createConnection($helper->getAccessToken(), '');
        }

    }


} /* FacebookConnector */
