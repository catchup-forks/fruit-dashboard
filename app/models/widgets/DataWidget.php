<?php

/* All classes that have interaction with data. */
abstract class DataWidget extends Widget implements iAjaxWidget
{
    use DefaultAjaxWidgetTrait;

    /**
     * Whether or not the criteria has changed.
     *
     * @var bool
     */
    protected $criteriaChanged = FALSE;

    /**
     * refreshWidget
     * Refreshing the widget data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    public function refreshWidget()
    {
        /* Setting to loading, and waiting for the collector to finish. */
        $this->setState('loading');
        $this->updateData();
        $this->setState('active');
    }

    /**
     * assignData
     * Assigning the data to the widget.
     */
    public function assignData()
    {
        $this->data()
            ->associate($this->getDescriptor()->getDataObject($this));
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
            $this->data->collect($options);
        } catch (ServiceException $e) {
            Log::error('An error occurred during collecting data on #' . $this->data->id );
            $this->data->setState('data_source_error');
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
        $this->data->setUpdatePeriod($interval);
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
        return $this->data->update_period;
    }

    /**
     * getData
     * Passing the job to the dataObject.
     */
    public function getData($postData=null)
    {
        return $this->data->decode();
    }

    /**
     * dataExists
     * Returns whether or not there is dat in the DB.
     */
    public function dataExists()
    {
        return ! is_null($this->data);
    }

    /**
     * checkIntegrity
     * adding data integrity check.
    */
    public function checkIntegrity()
    {
        parent::checkIntegrity();
        if ( ! $this->dataExists() ||
            ($this->data->decode() == FALSE && $this->data->state == 'active')) {
            $this->assignData();
            throw new WidgetException;
        }
        $this->setState($this->data->state);
    }

    /**
     * saveSettings
     * Transforming settings to JSON format. (validation done by view)
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=TRUE) {
        $changedFields = parent::saveSettings($inputSettings, $commit);
        if (array_intersect(static::getCriteriaFields(), $changedFields) &&
                $this->hasValidCriteria()) {
            $this->assignData();
            if ($this->state != 'setup_required') {
                $this->setState($this->data->state, FALSE);
            }
            $this->save();
        } else if (static::getCriteriaFields() == FALSE && ! $this->dataExists()) {
            $this->assignData();
        }
        return $changedFields;
    }

}
