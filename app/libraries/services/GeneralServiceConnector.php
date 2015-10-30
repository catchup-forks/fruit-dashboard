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
    abstract public function saveTokens(array $parameters);

    /**
     * disconnect
     * --------------------------------------------------
     * Disconnecting the user from the service.
     * --------------------------------------------------
     */
    public function disconnect() {
        /* Check valid connection */
        if ( ! $this->user->isServiceConnected(static::$service)) {
            throw new ServiceNotConnected(static::$service . ' service is not connected.', 1);
        }
        /* Deleting connection */
        $this->user->connections()->where('service', static::$service)->delete();

        /* Deleting all DataManagers */
        foreach ($this->user->dataObjects as $data) {
            if ($data->getDescriptor()->category == static::$service) {
                $data->delete();
            }
        }

        /* Deleting all widgets*/
        foreach ($this->user->widgets as $widget) {
            if ($widget->getDescriptor()->category == static::$service) {
                $widget->delete();
            }
        }
    }

    /**
     * getServiceName
     * Returns the name of the service.
     * --------------------------------------------------
     * @return string.
     * --------------------------------------------------
     */
    public static function getServiceName() {
        return static::$service;
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
        if ( ! $this->user->isServiceConnected(static::$service)) {
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
     * Creating the Data objects.
     * --------------------------------------------------
     * @param array $criteria
     * @return array
     * --------------------------------------------------
     */
    public function createDataObjects(array $criteria=array()) {
        $dataObjects = array();
        foreach(DataDescriptor::where('category', static::$service)->get() as $descriptor) {
            /* Creating widget instance. */
            $className = $descriptor->getCollectorClassName();

            /* No manager class found */
            if ( ! class_exists($className)) {
                continue;
            }

            /* Filtering criteria for manager. */
            $dataCriteria = array();
            try {
                foreach ($className::getCriteriaFields() as $field) {
                    if (array_key_exists($field, $criteria)) {
                        $dataCriteria[$field] = $criteria[$field];
                    } else {
                        throw new Exception("The criteria is not enough for this manager.", 1);
                    }
                }
            } catch (Exception $e) {
                continue;
            }

            /* Detecting previous managers. */
            $settingsCriteria = json_encode($dataCriteria);
            $data = $this->user->dataObjects()
                ->where('descriptor_id', $descriptor->id)
                ->where('criteria', $settingsCriteria)
                ->first();

            if ( ! is_null($data)) {
                /* Data found, leaving it alone. */
                array_push($dataObjects, $data);
                continue;
            }

            /* Creating data */
            $data = Data::create(array(
                'criteria'      => $settingsCriteria,
                'user_id'       => $this->user->id,
                'descriptor_id' => $descriptor->id
            ), FALSE);

            /* Assigning foreign values */
            $data->save();
            array_push($dataObjects, $data);
        }

        $this->populateData($criteria);

        return $dataObjects;
    }

    /**
     * populateData
     * --------------------------------------------------
     * Collecting the initial data from the service.
     * @param array $criteria
     * --------------------------------------------------
     */
    protected function populateData($criteria) {
        Queue::push('DataPopulator', array(
            'user_id'  => $this->user->id,
            'criteria' => $criteria,
            'service'  => static::$service
        ));
    }

} /* GeneralServiceConnector */
