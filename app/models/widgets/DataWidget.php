<?php

/* All classes that have interaction with data. */
abstract class DataWidget extends Widget
{
    /**
     * getData
     */
    public function getData() {
        return json_decode($this->data->raw_value, 1);
    }

    /**
     * checkDataIntegrity
     * Checking the DataIntegrity of widgets.
    */
    protected function checkDataIntegrity() {
        if (is_null($this->data)) {
            $this->initData();
            $data = Data::create(array('raw_value' => ''));
            $this->data()->associate($data);
        } else if ($this->data->raw_value == '') {
            $this->state = 'setup_required';
            $this->save();
        }
    }
}

?>
