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
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
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
     * saveTokens
     * Retrieving the access, and refresh tokens from authentication code.
     * --------------------------------------------------
     * @param array $parameters
     * @return None
     * @throws GoogleConnectFailed
     * --------------------------------------------------
     */
    public function saveTokens(array $parameters=array()) {
        $code = $parameters['auth_code'];
        /* Build and send POST request */
        $this->client->authenticate($code);
        $accessToken = $this->client->getAccessToken();
        $refreshToken = $this->client->getRefreshToken();
        $this->createConnection($accessToken, $refreshToken);
    }

    /**
     * refreshToken
     * Retrieving a new access token from refresh token.
     * --------------------------------------------------
     * @return None
     * --------------------------------------------------
     */
    public function refreshToken() {
        try {
            $connection = $this->getConnection();
        } catch (ServiceNotConnected $e) {
            return;
        }

        /* Fetching new access token */
        $this->client->refreshToken($connection->refresh_token);
        $connection->access_token = $this->client->getAccessToken();
        $connection->save();
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
        /* Handle refresh. */
        $token = json_decode($connection->access_token, 1);
        if ( ! is_array($token) || empty($token) || ! array_key_exists('created', $token) || ! array_key_exists('expires_in', $token)) {
            /* Disconnecting service if the token is invalid. */
            $this->disconnect();
        }

        if (($token['created'] + $token['expires_in']) < Carbon::now()->timestamp) {
            $this->refreshToken();
            $connection = $this->getConnection();
        }

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
