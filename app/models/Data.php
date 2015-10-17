<?php

class Data extends Eloquent
{
    // Escaping eloquent's plural naming.
    protected $table = 'data';

    // -- Fields -- //
    protected $fillable = array(
        'raw_value',
        'user_id',
        'descriptor_id',
        'criteria',
        'update_period',
        'state'
    );

    /**
     * The DataManager object.
     *
     * @var DataManager
     */
    protected $manager = null;

    /* -- Relations -- */
    public function descriptor() { return $this->belongsTo('WidgetDescriptor'); }
    public function user() { return $this->belongsTo('User'); }
    public function widgets() { return $this->hasMany('Widget'); }
    /* Optimized method, not using DB query */
    public function getDescriptor() {
        return WidgetDescriptor::find($this->descriptor_id);
    }

    /**
     * createFromWidget
     * Creating and returning a manager from a widget
     * --------------------------------------------------
     * @param Widget $widget
     * @return array
     * --------------------------------------------------
     */
    public static function createFromWidget($widget) {
        /* Only datawidgets are relevant */
        if ( ! $widget instanceof DataWidget) {
            return null;
        }

        /* Creating manager. */
        return self::create(array(
            'user_id'       => $widget->user()->id,
            'descriptor_id' => $widget->getDescriptor()->id,
            'criteria'      => json_encode($widget->getCriteria())
        ));
    }

    /**
     * create
     * Adding initializeData on creation.
     * --------------------------------------------------
     * @param array $attributes
     * @param bool $initialize,
     * @return array
     * --------------------------------------------------
     */
    public static function create(array $attributes, $initialize=TRUE) {
        if ( ! array_key_exists('raw_value', $attributes)) {
            $attributes['raw_value'] = json_encode(array());
        }
        $attributes['state'] = 'loading';
        $data = parent::create($attributes);

        /* Reinitializing data. (This will create the manager) */
        $data = Data::find($data->id);
        if ($initialize) {
            $data->initialize();
            $data->setState('active');
        }

        return $data;
    }

    /**
     * newFromBuilder
     * Override the base Model function to create a manager.
     * --------------------------------------------------
     * @param array $attributes
     * --------------------------------------------------
     */
    public function newFromBuilder($attributes=array()) {
        $data = new Data;
        $data->exists = TRUE;
        $data->setRawAttributes((array) $attributes, true);

        if ( ! empty($attributes->descriptor_id)) {
            /* Creating manager if descriptor is set. */
            $className = WidgetDescriptor::find($attributes->descriptor_id)
                ->getDMClassName();
            $data->manager = new $className($data);
        }
        return $data;
    }

    /**
     * getCriteria
     * Returning the settings that makes a difference among widgets.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getCriteria() {
        /* Criteria integrity check required. */
        return json_decode($this->criteria, 1);
    }

    /**
     * setState
     * Setting the state of the data.
     * --------------------------------------------------
     * @param string $state
     * --------------------------------------------------
    */
    public function setState($state) {
        if ($this->state == $state) {
            return;
        }
        Log::info("Changing state of data #" . $this->id . ' from ' . $this->state . ' to '. $state);
        $this->state = $state;
        $this->save();
        $this->setWidgetsState($state);
    }

    /**
     * setWidgetsState
     * Setting the corresponding widgets state.
     * --------------------------------------------------
     * @param string $state
     * --------------------------------------------------
     */
     public function setWidgetsState($state) {
        foreach ($this->widgets as $widget) {
            $widget->setState($state);
        }
     }

    /**
     * setUpdatePeriod
     * Setting the instace's update period.
     * --------------------------------------------------
     * @param int $updatePeriod
     * @return array
     * --------------------------------------------------
     */
    public function setUpdatePeriod($updatePeriod) {
        $this->update_period = $updatePeriod;
        $this->save();
    }

    /**
     * checkIntegrity
     * Checking the data integrity.
    */
    public function checkIntegrity() {
        if ($this->state == 'loading') {
            /* Populating is underway */
            $this->setWidgetsState('loading');
        } else if (is_null(json_decode($this->raw_value))) {
            /* No json in data, this is a problem. */
            $this->initialize();
            $this->setState('active');
        } else {
            $this->setState('active');
        }
    }

    /**
     * Delete
     * Deleting the data as well.
     */
     public function delete() {
        $this->widgets()->delete();
        return parent::delete();
    }

    /**
     * getManager
     * Returns the manager object.
     */
    public function getManager() {
        return $this->manager;
    }

    /**
     * decode
     * Returning the raw data json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function decode() {
        $data = json_decode($this->raw_value, 1);
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
    public function __call($method, $args) {
        return call_user_func_array(array($this->manager, $method), $args);
    }

}

?>