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
     * getClient
     * Returns the google client.
     * --------------------------------------------------
     * @return Google_Client object.
     * --------------------------------------------------
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * getTokens
     * Retrieving the access, and refresh tokens from authentication code.
     * --------------------------------------------------
     * @param array $parameters
     * @return None
     * @throws GoogleConnectFailed
     * --------------------------------------------------
     */
    public function getTokens(array $parameters=array()) {
        $code = $parameters['auth_code'];
        /* Build and send POST request */
        $this->client->authenticate($code);
        $accessToken = $this->client->getAccessToken();
        $this->createConnection($accessToken, '');
    }

    /**
     * connect
     * Sets up a google connection with the AccessToken.
     * --------------------------------------------------
     * @throws ServiceNotConnected
     * --------------------------------------------------
     */
    public function connect() {
        /* Get access token from DB. */
        $connection = $this->getConnection();
        $this->client->setAccessToken($connection->access_token);
    }

    /**
     * disconnect
     * Revoking google access.
     * --------------------------------------------------
     * @throws ServiceNotConnected
     * --------------------------------------------------
     */
    public function disconnect() {
        $this->client->revokeToken();
        parent::disconnect();
    }

} /* GoogleConnector */
