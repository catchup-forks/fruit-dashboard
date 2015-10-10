<?php

/* All classes that have interaction with data. */
abstract class CronWidget extends Widget implements iAjaxWidget
{
    use DefaultAjaxWidgetTrait;
    /* Custom relation. */
    protected function dataManager() {
        if ( ! $this->dataExists()) {
            return null;
        }
        return $this->data->manager;
    }

    /**
     * refreshWidget
     * Refreshing the widget data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    public function refreshWidget() {
        /* Setting to loading, and waiting for the collector to finish. */
        $this->setState('loading');
        $this->updateData();
        $this->setState('active');
    }

    /**
     * save
     * Looking for managers.
     * --------------------------------------------------
     * @param array $options
     * @return null
     * --------------------------------------------------
    */
    public function save(array $options=array()) {
        parent::save($options);

        if ( ! isset($options['skipManager']) || $options['skipManager'] == FALSE) {
            $dataManager = $this->descriptor->getDataManager($this);
            if ( ! is_null($dataManager)) {
                $this->data()->associate($dataManager->data);
                parent::save($options);
            }
        }

        return TRUE;
    }

    /*
     * dataExists
     * Checking if data/manager exists
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
    */
    protected function dataExists() {
        return  ! (is_null($this->data) || is_null($this->data->manager));
    }

    /**
     * updateData
     * Refreshing the widget data.
     * --------------------------------------------------
     * @param array options
     * @return string
     * --------------------------------------------------
    */
    public function updateData(array $options=array()) {
        $this->dataManager()->collectData($options);
    }

    /**
     * setUpdatePeriod
     * Setting the data collection period.
     * --------------------------------------------------
     * @param int interval
     * --------------------------------------------------
    */
    public function setUpdatePeriod($interval) {
        $this->dataManager()->setUpdatePeriod($interval);
    }

    /**
     * getUpdatePeriod
     * Setting the data collection period.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
    */
    public function getUpdatePeriod() {
        return $this->dataManager()->update_period;
    }

    /**
     * getData
     * Passing the job to the DataManager
     */
    public function getData($postData=null) {
        return $this->dataManager()->getData();
    }

    /**
     * checkIntegrity
     * adding data integrity check.
    */
    public function checkIntegrity() {
        parent::checkIntegrity();
        if ( ! $this->dataExists()) {
            $this->save();
        }
    }

}