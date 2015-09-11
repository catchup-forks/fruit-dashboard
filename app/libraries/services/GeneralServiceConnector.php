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
    abstract protected function populateData();

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

        /* Deleting all widgets*/
        foreach ($this->user->widgets as $widget) {
            if ($widget->descriptor->category == static::$service) {
                $widget->delete();
            }
        }
        /* Deleting all DataManagers */
        foreach ($this->user->dataManagers as $dataManager) {
            if ($dataManager->descriptor->category == static::$service) {
                /* Saving data while it is accessible. */
                $dataID = $dataManager->data->id;
                $dataManager->delete();
                Data::find($dataID)->delete();
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

    /**
     * saveConnection
     * Saving the tokens, creating data managers.
     */
    public function saveConnection(array $parameters=array()) {

        /* Saving tokens */
        $this->getTokens($parameters);

        /* Creating dataManagers */
        $this->createDataManagers();

        /* Getting data */
        $this->populateData();
    }

    /**
     * Creating the dataManagers.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function createDataManagers() {
        $dataManagers = array();
        foreach(WidgetDescriptor::where('category', static::$service)->get() as $descriptor) {
            /* Creating widget instance. */
            $className = str_replace('Widget', 'DataManager', $descriptor->getClassName());

            /* No manager found */
            if ( ! class_exists($className)) {
                continue;
            }

            /* Creating data */
            $data = Data::create(array('raw_value' => 'loading'));

            /* Creating DataManager instance */
            $dataManager = new $className;
            $dataManager->descriptor()->associate($descriptor);
            $dataManager->user()->associate($this->user);

            /* Assigning data */
            $dataManager->data()->associate($data);
            $dataManager->save();

            array_push($dataManagers, $dataManager);
        }
        return $dataManagers;
    }


} /* GeneralServiceConnector */
