<?php

/* All classes that have interaction with data. */
abstract class DataWidget extends Widget implements iAjaxWidget
{
    use DefaultAjaxWidgetTrait;

    /* -- Relations -- */
    protected function dataObject() { return $this->belongsTo('Data', 'data_id'); }

    /**
     * Whether or not the criteria has changed.
     *
     * @var bool
     */
    protected $criteriaChanged = FALSE;

    /**
     * The DM. Used to communicate with the DB data.
     *
     * @var DataManager
     */
    protected $dataManager = NULL;

    /**
     * setupDataManager
     * Setting up the datamanager
     * --------------------------------------------------
     * @return DataManager
     * --------------------------------------------------
     */
    protected function setupDataManager() 
    {
        $dataObject = $this->dataObject()->first(Data::getMetaFields());

        if ($dataObject) {
            $manager = $dataObject->getManager();

            return $manager;
        }

        return NULL;
    }


    /**
     * refreshWidget
     * Refreshing the widget data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    public function refreshWidget()
    {
        $this->setState('loading');
    
        $this->updateData();
    
        $this->setState('active');
    }

    /**
     * assignData
     * Assigning the data to the widget.
     * --------------------------------------------------
     * @param boolean $commit
     * @return boolean
     * --------------------------------------------------
     */
    public function assignData($commit=TRUE)
    {
        if ( ! $this->hasValidCriteria()) {
            return FALSE;
        }
        
        $dataObject = $this->getDescriptor()->getDataObject($this);

        $this->dataObject()->associate($dataObject);

        $this->setState($dataObject->state, $commit);

        return TRUE;
    }

    /**
     * updateData
     * Refreshing the widget data.
     * --------------------------------------------------
     * @param array options
     * @return string
     * --------------------------------------------------
    */
    public function updateData(array $options=array())
    {
        try {
            $this->dataManager->collect($options);
        } catch (ServiceException $e) {
            Log::error('An error occurred during collecting data on #' . $this->data_id );
            $this->dataObject->setState('data_source_error');
        }
    }

    /**
     * setUpdatePeriod
     * Setting the data collection period.
     * --------------------------------------------------
     * @param int interval
     * --------------------------------------------------
    */
    public function setUpdatePeriod($interval)
    {
        $this->dataObject->setUpdatePeriod($interval);
    }

    /**
     * getUpdatePeriod
     * Setting the data collection period.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
    */
    public function getUpdatePeriod()
    {
        return $this->dataObject->update_period;
    }

    /**
     * dataExists
     * Returns whether or not there is dat in the DB.
     */
    public function dataExists()
    {
        return ! is_null($this->data_id);
    }

    /**
     * checkIntegrity
     * adding data integrity check.
    */
    public function checkIntegrity()
    {
        parent::checkIntegrity();

        if ( ! $this->dataExists() && $this->assignData() == FALSE) {
            throw new WidgetException;
        }
        dd($this);

        $this->setState($this->dataObject->state);

        dd($this);
    }

    /**
     * saveSettings
     * Assigning data if criteria has changed.
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=TRUE) 
    {
        $changedFields = parent::saveSettings($inputSettings, $commit);

        if (array_intersect(static::getCriteriaFields(), $changedFields)) {
            $this->assignData($commit);
        }

        return $changedFields;
    }

    /**
     * onCreate
     * Creating dataManager.
     * --------------------------------------------------
     * @param array $attributes
     * --------------------------------------------------
     */
    protected function onCreate() 
    {
        if ($this->dataExists()) {
            $this->dataManager = $this->setupDataManager();
        }
    }

}
