<?php

/**
* --------------------------------------------------------------------------
* GeneralServiceConnector:
*     Abstract class used mainly as an interface for service connectors.
* --------------------------------------------------------------------------
*/

abstract class GeneralServiceConnector
{
    /* -- Class properties -- */
    protected $user;
    protected static $service = null;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
    }

    abstract public function connect();
    abstract public function disconnect();

    /**
     * createConnection
     * --------------------------------------------------
     * Creating a connection on the DB level.
     * @param string $accessToken
     * @param string $refreshToken
     * --------------------------------------------------
     */
    protected function createConnection($accessToken, $refreshToken) {
        /* Deleting all previos connections, and stripe widgets. */
        $this->user->connections()->where('service', static::$service)->delete();

        /* Creating a Connection instance, and saving to DB. */
        $connection = new Connection(array(
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'service'       => static::$service,
        ));
        $connection->user()->associate($this->user);
        $connection->save();
        return $connection;
    }

} /* GeneralServiceConnector */
