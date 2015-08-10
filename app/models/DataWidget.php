<?php

abstract class DataWidget extends Widget
{
    abstract public function createDataScheme(); // called, when there is no/corrupt data in database
    abstract public function getData(); // default return, when there is a query for data.
    abstract public function collectData();

    /**
     * handleAjax
     * --------------------------------------------------
     * Handling general ajax request.
     * @param $postData the data from the request.
     * @return mixed array if state_query 0 otherwise
     * --------------------------------------------------
    */
    public function handleAjax($postData) {
        if (isset($postData['state_query']) && $postData['state_query']) {
            if ($this->state == 'loading') {
                return array('ready' => FALSE);
            } else if($this->state == 'active') {
                return array(
                    'ready' => TRUE,
                    'data'  => $this->getData()
                );
            } else {
                return array('ready' => FALSE);
            }
        }
        if (isset($postData['refresh_data']) && $postData['refresh_data']) {
            /* Setting state to loading. */
            $this->state = 'loading';
            $this->save();

            $this->collectData();

            $this->state = 'active';
            $this->save();
        }
        return 0;
    }
}

?>
