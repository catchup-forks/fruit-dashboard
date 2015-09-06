<?php

/* All classes that have interaction with data. */
abstract class CronWidget extends DataWidget implements iAjaxWidget
{
    public static $criteriaSettings = array();

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

        /* Something else, should be handled by specific widget. */
        return $this->handleCustomAjax($postData);
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
        $this->collectData();

        /* Faling back to active. */
        $this->state = 'active';
        $this->save();
    }

    /**
     * collectData
     * Passing the job to the DataManager
     */
    public function collectData() {
        return $this->data->manager->getSpecific()->collectData();
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
            }
        }
        return $settings;
    }

    /**
     * getData
     * Passing the job to the DataManager
     */
    public function getData($postData=null) {
        return $this->data->manager->getSpecific()->getData();
    }

    /**
     * checkDataIntegrity
     * Checking the DataIntegrity of widgets.
    */
    protected function checkDataIntegrity() {
        if (is_null($this->data) || is_null($this->data->manager)) {
            /* No data is assigned, let's hope a save will fix it. */
            $this->save();
            if (is_null($this->data)) {
                /* Still not working */
                $this->state = 'setup_required';
                $this->save();
            }
        } else if ($this->data->raw_value == '') {
            $this->state = 'setup_required';
            $this->save();
        } else if (json_decode($this->data->raw_value) == FALSE) {
            $this->state = 'loading';
            $this->save();
        }
    }

    /**
     * handleCustomAjax
     * Dummy custom ajax handler.
     * --------------------------------------------------
     * @param array $postData
     * @return null
     * --------------------------------------------------
    */
    protected function handleCustomAjax($postData) {
        return null;
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
                $this->data()->associate($dataManager->getSpecific()->data);
            }
        }

        return parent::save($options);
    }

}