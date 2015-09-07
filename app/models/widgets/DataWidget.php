<?php

/* All classes that have interaction with data. */
abstract class DataWidget extends Widget
{
    abstract protected function createDataScheme();

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
        $emptyData = $this->createDataScheme();
        /* Data not set */
        if (is_null($this->data)) {
            $data = Data::create(array('raw_value' => json_encode($emptyData)));
            $this->data()->associate($data);
            $this->save();
        } else if ($this->data->raw_value == '' || array_keys($emptyData) != array_keys(json_decode($this->data->raw_value, 1))) {
            $this->data->raw_value = json_encode($emptyData);
            $this->data->save();
        }
    }
}

?>
