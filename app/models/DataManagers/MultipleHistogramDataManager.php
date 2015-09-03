<?php

/* This class is responsible for histogram data collection. */
abstract class MultipleHistogramDataManager extends HistogramDataManager
{
    /**
     * formatData
     * Returning the last data in the histogram.
     * --------------------------------------------------
     * @param Carbon $date
     * @param mixed $data
     * @return array
     * --------------------------------------------------
     */
     protected function formatData($date, $data) {
        return array('date' => $date, 'value' => $newValue);
     }

    /**
     * getData
     * Returning the raw data json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getData() {
        return json_decode($this->data->raw_value, 1)['data'];
    }

    /**
     * getDataSets
     * Returning the dataSets json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getDataSets() {
        return json_decode($this->data->raw_value, 1)['datasets'];
    }

    /**
     * saveData
     * Saving the data to DB
     * --------------------------------------------------
     * @param data $data
     * --------------------------------------------------
     */
     protected function saveData($data) {
        /* Getting dataSets */
        $this->data->raw_value = json_encode(array(
            'datasets' => $this->getDataSets,
            'data'     => $data
        ));
        $this->data->save();
     }

}