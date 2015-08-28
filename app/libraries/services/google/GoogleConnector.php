<?php

/**
* --------------------------------------------------------------------------
* GoogleConnector:
*       Wrapper functions for Google connection
* Usage:
* --------------------------------------------------------------------------
*/

abstract class GoogleConnector extends GeneralServiceConnector
{
    /* -- Class properties -- */
    protected $client = null;
    protected static $scope   = null;

    /* -- Constructor -- */
    function __construct($user) {
        parent::__construct($user);
        $this->client = static::createClient();
    }

    /**
     * getClient
     * Returns the google client.
     * --------------------------------------------------
     * @return Google_Client object.
     * --------------------------------------------------
     */
    public function getClient() {
        return self::$client;
    }

    /**
     * getConnectUrl
     * Returns the google connect url, based on config.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getConnectUrl() {
        $client = static::createClient();
        return $client->createAuthUrl();
    }

    /**
     * createClient
     * Returning a google client.
     * --------------------------------------------------
     * @return Google_Client object.
     * --------------------------------------------------
     */
    public static function createClient() {
        $client = new Google_Client();
        $client->setAuthConfigFile(base_path($_ENV['GOOGLE_SECRET_JSON']));
        $client->addScope(static::$scope);
        $client->setRedirectUri(route('service.' . static::$service . '.connect'));
        return $client;
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getTokens
     * Retrieving the access, and refresh tokens from authentication code.
     * --------------------------------------------------
     * @param string $code The returned code by google.
     * @return None
     * @throws GoogleConnectFailed
     * --------------------------------------------------
     */
    public function getTokens($code) {
        /* Build and send POST request */
        $this->client->authenticate($code);
        $accessToken = $this->client->getAccessToken();
        $this->createConnection($accessToken, '');
    }

    /**
     * connect
     * Sets up a google connection with the AccessToken.
     * --------------------------------------------------
     * @throws GoogleNotConnected
     * --------------------------------------------------
     */
    public function connect() {
        /* Get access token from DB. */
        $connection = $this->getConnection();
        $this->client->setAccessToken($connection->access_token);

    }

} /* GoogleConnector */
