<?php

/* All classes that have interaction with data. */
abstract class CronWidget extends Widget implements iAjaxWidget
{
    public static $criteriaSettings = array();

    /* Custom relation. */
    public function dataManager() { return $this->data->manager->getSpecific(); }

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
            /* Get state query signal */
            if ($this->state == 'loading') {
                return array('ready' => FALSE);
            } else if($this->state == 'active') {
                return array(
                    'ready' => TRUE,
                    'data'  => $this->getData($postData)
                );
            } else {
                return array('ready' => FALSE);
            }
        }
        if (isset($postData['refresh_data']) && $postData['refresh_data']) {
            /* Refresh signal */
            $this->refreshWidget();
        }
    }

    /**
     * Refreshing the widget data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    public function refreshWidget() {
        $this->state = 'loading';
        $this->save();

        /* Refreshing widget data. */
        $this->dataManager()->collectData();

        /* Faling back to active. */
        $this->state = 'active';
        $this->save();
    }

    /**
     * getCriteria
     * Returning the settings that makes a difference among widgets.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getCriteria() {
        $settings = array();
        foreach (static::$criteriaSettings as $key) {
            if (array_key_exists($key, $this->getSettings())) {
                $settings[$key] = $this->getSettings()[$key];
            } else {
                return array();
            }
        }
        return $settings;
    }

    /**
     * getData
     * Passing the job to the DataManager
     */
    public function getData($postData=null) {
        return $this->dataManager()->getData();
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
        $widget = parent::save($options);

        if ( ! isset($options['skipManager']) || $options['skipManager'] == FALSE) {
            $dataManager = $this->descriptor->getDataManager($this);
            if ( ! is_null($dataManager)) {
                $this->data()->associate($dataManager->data);
            }
            $widget = parent::save($options);
        }

        return $widget;
    }

    /**
     * checkDataIntegrity
     * Checking the DataIntegrity of widgets.
    */
    protected function checkDataIntegrity() {
        if ( ! $this->dataExists()) {
            /* No data/datamanager is assigned */
            $this->save();
            if ( ! $this->dataExists()) {
                /* Still not working */
                $this->setState('setup_required');
            }
        } else if ($this->data->raw_value == 'loading') {
            /* Populating is underway */
            $this->setState('loading');
        } else if (is_null(json_decode($this->data->raw_value)) || ! $this->hasValidScheme()) {
            /* No json in data, this is a problem. */
            $this->dataManager()->initializeData();
        }
    }

    /**
     * dataExists
     * Checking if data/manager exists
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
    */
    private function dataExists() {
        return  ! (is_null($this->data) || is_null($this->dataManager()));
    }

    /**
     * hasValidScheme
     * Checking if the scheme is valid in the data.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
    */
    private function hasValidScheme() {
        $scheme = $this->dataManager()->getDataScheme();
        $dataScheme = json_decode($this->data->raw_value, 1);
        if ( ! is_array($dataScheme)) {
            return FALSE;
        }
        /* Iterating through the keys */
        foreach ($scheme as $key) {
            if ( ! array_key_exists($key, $dataScheme)) {
                return FALSE;
            }
        }
        return TRUE;
    }

}