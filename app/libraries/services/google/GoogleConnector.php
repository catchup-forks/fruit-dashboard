<?php

/**
* --------------------------------------------------------------------------
* GoogleConnector:
*       Wrapper functions for Google connection
* Usage:
* --------------------------------------------------------------------------
*/

class GoogleConnector extends GeneralServiceConnector
{
    /* -- Class properties -- */
    private $client;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
        $this->client = new Google_Client();
        $this->client->setAuthConfigFile($_ENV['GOOGLE_SECRET_JSON']);
        $this->client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
        $this->client->setRedirectUri(route('service.google.connect'));
    }

    /**
     * getClient
     * --------------------------------------------------
     * Returns the google client.
     * @return Google_Client object.
     * --------------------------------------------------
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * connect
     * --------------------------------------------------
     * Sets up a google connection with the AccessToken.
     * @throws GoogleNotConnected
     * --------------------------------------------------
     */
    public function connect() {
        /* Check valid connection */
        if (!$this->user->isServiceConnected('google')) {
            throw new GoogleNotConnected();
        }

        /* Get access token from DB. */
        $token = $this->user->connections()
            ->where('service', 'google') ->first()->access_token;
        $this->client->setAccessToken($token);

    }
    /**
     * getGoogleConnectURL
     * --------------------------------------------------
     * Returns the google connect url, based on config.
     * @return array
     * --------------------------------------------------
     */
    public function getGoogleConnectUrl() {
        return $this->client->createAuthUrl();
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getTokens
     * --------------------------------------------------
     * Retrieving the access, and refresh tokens from authentication code.
     * @param string $code The returned code by google.
     * @return None
     * @throws GoogleConnectFailed
     * --------------------------------------------------
     */
    public function getTokens($code) {
        /* Build and send POST request */
        $this->client->authenticate($code);
        $accessToken = $this->client->getAccessToken();

        /* Deleting all previos connections. */
        $this->user->connections()->where('service', 'google')->delete();

        /* Creating a Connection instance, and saving to DB. */
        $connection = new Connection(array(
            'access_token'  => $accessToken,
            'refresh_token' => '',
            'service'       => 'google',
        ));
        $connection->user()->associate($this->user);
        $connection->save();
    }

    /**
     * disconnect
     * --------------------------------------------------
     * Disconnecting the user from google.
     * @throws GoogleNotConnected
     * --------------------------------------------------
     */
    public function disconnect() {
        /* Check valid connection */
        if (!$this->user->isServiceConnected('google')) {
            throw new GoogleNotConnected();
        }
        /* Deleting connection */
        $this->user->connections()->where('service', 'google')->delete();

        /* Deleting all widgets, plans, subscribtions */
        foreach ($this->user->widgets() as $widget) {
            if ($widget->descriptor->category == 'google') {

                /* Saving data while it is accessible. */
                $dataID = 0;
                if (!is_null($widget->data)) {
                    $dataID = $widget->data->id;
                }

                $widget->delete();

                /* Deleting data if it was present. */
                if ($dataID > 0) {
                    Data::find($dataID)->delete();
                }
            }
        }
    }

} /* GoogleConnector */
