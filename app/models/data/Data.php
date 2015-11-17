<?php

class Data extends Eloquent
{
    /* Collector functions. */
    private static $collectorFunctions = array('initialize', 'collect');

    /* Escaping eloquent's plural naming. */
    protected $table = 'data';

    /* -- Fields -- */
    protected $fillable = array(
        'raw_value',
        'user_id',
        'descriptor_id',
        'criteria',
        'update_period',
        'state'
    );

    /* -- Relations -- */
    public function descriptor() { return $this->belongsTo('DataDescriptor', 'descriptor_id'); }
    public function user() {
        return User::remember(120)->find($this->user_id);
    }

    /* Optimized method, not using DB query */
    public function getDescriptor() {
        return DataDescriptor::find($this->descriptor_id);
    }

    /**
     * createFromWidget
     * Creating and returning a manager from a widget
     * --------------------------------------------------
     * @param Widget $widget
     * @param string $category
     * @param string $dataType
     * @return array
     * --------------------------------------------------
     */
    public static function createFromWidget($widget, $category, $dataType)
    {
        /* Finding the corresponding descriptor. */
        $user = $widget->user();
        $criteria = $widget->getCriteria();
        $descriptor = DataDescriptor::where('type', $dataType)
            ->where('category', $category)
            ->first();

        if (is_null($descriptor) || self::exists($user, $descriptor->id, $criteria)) {
            /* Skip creation, if the descriptor is not found, or the data already exists. */
            return;
        }

        /* Creating data. */
        return self::create(array(
            'user_id'       => $widget->user()->id,
            'descriptor_id' => $descriptor->id,
            'criteria'      => json_encode($criteria)
        ));
    }

    /**
     * exists
     * Return whether or not a data with this criteria exists.
     * --------------------------------------------------
     * @param User $user
     * @param int $descriptorId
     * @param array $criteria
     * @return array
     * --------------------------------------------------
     */
    public static function exists($user, $descriptorId, $criteria)
    {
        return ! is_null($user->dataObjects()
            ->where('descriptor_id', $descriptorId)
            ->where('criteria', json_encode($criteria)) // Criteria breakdown required.
            ->first()
        );
    }

    /**
     * create
     * Add initialize on creation.
     * --------------------------------------------------
     * @param array $attributes
     * @param bool $initialize,
     * @return array
     * --------------------------------------------------
     */
    public static function create(array $attributes, $initialize=true)
    {
        if ( ! array_key_exists('raw_value', $attributes)) {
            $attributes['raw_value'] = json_encode(array());
        }

        $attributes['state'] = 'loading';
        $data = parent::create($attributes);

        /* Reinitializing data. (This will create the manager) */
        $data = Data::find($data->id);

        if ($initialize) {
            try {
                $data->initialize();
                $data->setState('active');
            } catch (ServiceException $e) {
                $data->setState('data_source_error');
            }
        }

        return $data;
    }

    /**
     * getCriteria
     * Return the settings that makes a difference among widgets.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getCriteria()
    {
        return json_decode($this->criteria, 1);
    }

    /**
     * setState
     * Setting the state of the data.
     * --------------------------------------------------
     * @param string $state
     * --------------------------------------------------
    */
    public function setState($state)
    {
        if ($this->state == $state) {
            return;
        }

        if ( ! App::environment('production')) {
            Log::info("Changing state of data #" . $this->id . ' from ' . $this->state . ' to '. $state);
        }

        $this->state = $state;

        $this->save();
    }

    /**
     * setUpdatePeriod
     * Setting the instace's update period.
     * --------------------------------------------------
     * @param int $updatePeriod
     * @return array
     * --------------------------------------------------
     */
    public function setUpdatePeriod($updatePeriod)
    {
        $this->update_period = $updatePeriod;
        $this->save();
    }

    /**
     * checkIntegrity
     * Checking the data integrity.
    */
    public function checkIntegrity()
    {
        $decodedData = json_decode($this->raw_value, 1);

        if ( ! is_array($decodedData) || empty($decodedData) ) {
            /* No json in data, this is a problem. */
            try {
                $this->initialize();
                $this->setState('active');
            } catch (ServiceException $e) {
                Log::error($e->getMessage());
                $this->setState('data_source_error');
            }
        } else if ($this->state == 'data_source_error'){
            /* Something went wrong with the latest data collection. */
            try {
                $this->collect();
                $this->setState('active');
            } catch (ServiceException $e) {}
        } else {
            /* Everything seems to be well. */
            $this->setState('active');
        }
    }

    /**
     * decode
     * Return the raw data json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function decode()
    {
        /* Getting the data from DB. */
        $raw_value = DB::table('data')
            ->where('id', $this->id)
            ->pluck('raw_value');

        /* Running JSON decoder. */
        $data = json_decode($raw_value, 1);

        if ( ! is_array($data)) {
            return array();
        }

        return $data;
    }

    /**
     * saveData
     * Saving the input data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function saveData(array $data) {
        $this->raw_value = json_encode($data, 1);
        $this->save();
    }

    /**
     * __call
     * PHP's magic method, all functions will be passed
     * automatically to the manager.
     * --------------------------------------------------
     * @param string $method
     * @param array $args
     * --------------------------------------------------
     */
    public function __call($method, $args)
    {
        /* Collector functions passed to specific collector */
        if (in_array($method, self::$collectorFunctions)) {
            $collector = $this->createCollector();
            return call_user_func_array(array($collector, $method), $args);
        }
        throw new Exception('Method does not exist');
    }

    /**
     * createCollector
     * Creating a dataCollector instance.
     * --------------------------------------------------
     * @return DataCollector.
     * --------------------------------------------------
    */
    private function createCollector()
    {
        $className = $this->getDescriptor()->getCollectorClassName();
        $collector = new $className($this);
        return $collector;
    }

    /**
     * save
     * Overriding save to add descriptor automatically.
     * --------------------------------------------------
     * @return the saved object.
     * @throws DescriptorDoesNotExist
     * --------------------------------------------------
    */
    public function save(array $options=array())
    {
        /* Notify user about the change */
        $this->user()->updateDashboardCache();
        return parent::save($options);
    }
}

?>
