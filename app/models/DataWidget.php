<?php

abstract class DataWidget extends Widget
{
    abstract public function collectData(); // This function recalculates current widget data.
    abstract public function getData($postData=null); // Returning widget data.

    /**
     * handleAjax
     * --------------------------------------------------
     * Handling general ajax request.
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
            $this->state = 'loading';
            $this->save();

            /* Refreshing widget data. */
            $this->collectData();

            /* Faling back to active. */
            $this->state = 'active';
            $this->save();
        }

        /* Something else, should be handled by specific widget. */
        return $this->handleCustomAjax($postData);
    }

    /**
     * createDataScheme
     * --------------------------------------------------
     * Returning a default scheme for the data.
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function createDataScheme() {
        return json_encode(array());
    }

    /**
     * handleCustomAjax
     * --------------------------------------------------
     * Dummy custom ajax handler.
     * @param array $postData
     * @return null
     * --------------------------------------------------
    */
    protected function handleCustomAjax($postData) {
        return null;
    }
}

?>
