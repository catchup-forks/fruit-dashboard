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

    /**
     * disconnect
     * --------------------------------------------------
     * Disconnecting the user from the service.
     * --------------------------------------------------
     */
    public function disconnect() {
        /* Check valid connection */
        if (!$this->user->isServiceConnected(static::$service)) {
            throw new ServiceNotConnected(static::$service . ' service is not connected.', 1);
        }
        /* Deleting connection */
        $this->user->connections()->where('service', static::$service)->delete();

        /* Deleting all widgets, plans, subscribtions */
        foreach ($this->user->widgets() as $widget) {
            if ($widget->descriptor->category == static::$service) {

                /* Saving data while it is accessible. */
                $dataID = 0;
                if (!is_null($widget->data)) {
                    $dataID = $widget->data->id;
                }

                $widget->delete();

                /* Deleting data if it was present. */
                if ($dataID > 0) {
                    /* Deleting all widgets assigned to it. */
                    Widget::where('data_id', $dataID)->delete();
                    Data::find($dataID)->delete();
                }
            }
        }
    }

    /**
     * getConnection
     * Getting the user's specific connection.
     * --------------------------------------------------
     * @return Connection
     * @throws ServiceNotConnected
     * --------------------------------------------------
     */
    protected function getConnection() {
        /* Check valid connection */
        if (!$this->user->isServiceConnected(static::$service)) {
            throw new ServiceNotConnected(static::$service . ' service is not connected.', 1);
        }
        $connection = $this->user->connections()
            ->where('service', static::$service) ->first();
        return $connection;
    }

    /**
     * createConnection
     * Creating a connection on the DB level.
     * --------------------------------------------------
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
