<?php

/* All classes that have interaction with data. */
abstract class DataWidget extends Widget implements iAjaxWidget
{
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
     * handleAjax
     * Handling general ajax request.
     * --------------------------------------------------
     * @param array $postData
     * @return mixed
     * --------------------------------------------------
    */
    public function handleAjax($postData) {
        if (isset($postData['state_query']) && $postData['state_query']) {
            /* Got state query signal */
            if ($this->state == 'loading') {
                return array('ready' => FALSE);
            } else if($this->state == 'active') {
                /* Rerendering the widget */
                $view = View::make($this->getDescriptor()->getTemplateName())
                    ->with('widget', $this->getTemplateData());
                return array(
                    'ready' => TRUE,
                    'data'  => $this->getData($postData),
                    'html'  => $view->render()
                );
            } else {
                return array('ready' => FALSE);
            }
        }
        if (isset($postData['refresh_data']) && $postData['refresh_data']) {
            /* Refresh signal */
            try {
                $this->refreshWidget();
            } catch (ServiceException $e) {
                Log::error($e->getMessage());
                return array('status'  => FALSE,
                             'message' => 'We couldn\'t refresh your data, because the service is unavailable.');
            }
        }
    }

    /**
     * buildData
     * Calling the manager's build.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function buildData() 
    {
        return $this->dataManager->build();
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
     * refreshWidget
     * Refreshing the widget data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    protected function refreshWidget()
    {
        $this->setState('loading');
    
        $this->updateData();
    
        $this->setState('active');
    }


    /**
     * setupDataManager
     * Setting up the datamanager
     * --------------------------------------------------
     * @return DataManager
     * --------------------------------------------------
     */
    protected function setupDataManager() 
    {
        $dataObject = $this->dataObject;

        if ($dataObject) {
            $manager = $dataObject->getManager();

            return $manager;
        }

        return NULL;
    }

    /**
     * assignData
     * Assigning the data to the widget.
     * --------------------------------------------------
     * @param boolean $commit
     * @return boolean
     * --------------------------------------------------
     */
    protected function assignData($commit=TRUE)
    {
        if ( ! $this->hasValidCriteria()) {
            return FALSE;
        }
        
        $dataObject = $this->getDescriptor()->getDataObject($this);

        if (is_null($dataObject)) {
            return FALSE;
        }

        $this->dataObject()->associate($dataObject);

        $this->setState($dataObject->state, FALSE);

        if ($commit) {
            $this->save();
        }

        return TRUE;
    }

    /**
     * getData
     * Returning the filtered data by the manager.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    protected function getData(array $options=array())
    {
        return $this->dataManager->build($options);
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
     * checkIntegrity
     * adding data integrity check.
    */
    public function checkIntegrity()
    {
        parent::checkIntegrity();

        if ( ! $this->dataExists() && $this->assignData() == FALSE) {
            throw new WidgetException;
        }

        $this->dataObject->checkIntegrity();

        $this->setState($this->dataObject->state);
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
     * dataExists
     * Returns whether or not there is dat in the DB.
     */
    protected function dataExists()
    {
        return ! is_null($this->data_id);
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

            if (is_null($this->dataManager)) {
                throw new WidgetException('DataManager not found');
            }
        } 
    }

}
