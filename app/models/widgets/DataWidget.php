<?php

abstract class DataWidget extends Widget
{
    abstract public function collectData(); // This function recalculates current widget data.
    abstract public function getData($postData=null); // Returning widget data.

    protected static $criteriaSettings = array();

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
     * createDataScheme
     * Returning a default scheme for the data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    public function createDataScheme() {
        return json_encode(array());
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
}

?>
