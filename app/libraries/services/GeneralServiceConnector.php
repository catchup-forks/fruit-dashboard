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
        foreach ($this->user->dataManagers as $dataManager) {
            if ($dataManager->getDescriptor()->category == static::$service) {
                $dataManager->delete();
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
     * Creating the dataManagers.
     * --------------------------------------------------
     * @param array $criteria
     * @return array
     * --------------------------------------------------
     */
    public function createDataManagers(array $criteria=array()) {
        $dataManagers = array();
        foreach(WidgetDescriptor::where('category', static::$service)->orderBy('number', 'asc')->get() as $descriptor) {
            /* Creating widget instance. */
            $className = $descriptor->getDMClassName();

            /* No manager class found */
            if ( ! class_exists($className)) {
                continue;
            }

            /* Filtering criteria for manager. */
            $dmCriteria = array();
            try {
                foreach ($className::getCriteriaFields() as $field) {
                    if (array_key_exists($field, $criteria)) {
                        $dmCriteria[$field] = $criteria[$field];
                    } else {
                        throw new Exception("The criteria is not enough for this manager.", 1);
                    }
                }
            } catch (Exception $e) {
                continue;
            }

            /* Detecting previous managers. */
            $settingsCriteria = json_encode($dmCriteria);
            $manager = $this->user->dataManagers()->where('descriptor_id', $descriptor->id)->where('settings_criteria', $settingsCriteria)->first();

            if ( ! is_null($manager)) {
                /* Manager found, leaving it alone. */
                array_push($dataManagers, $manager);
                continue;
            }

            /* Creating data */
            $data = Data::create(array('raw_value' => '[]'));

            /* Creating DataManager instance */
            $dataManager = new $className(array(
                'settings_criteria' => json_encode($dmCriteria),
                'last_updated'      => Carbon::now(),
                'state'             => 'loading'
            ));

            /* Assigning foreign values */
            $dataManager->descriptor()->associate($descriptor);
            $dataManager->user()->associate($this->user);
            $dataManager->data()->associate($data);

            $dataManager->save();

            array_push($dataManagers, $dataManager);
        }

        $this->populateData($criteria);

        return $dataManagers;
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
