<?php

/* This class is responsible for histogram data collection. */
abstract class MultipleHistogramDataManager extends HistogramDataManager
{
    /**
     * formatData
     * Returning the last data in the histogram.
     * --------------------------------------------------
     * @param Carbon $date
     * @param mixed $value
     * @return array
     * --------------------------------------------------
     */
     protected function formatData($date, $value) {
        return array('date' => $date, 'data_0' => $value);
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
            'datasets' => $this->getDataSets(),
            'data'     => $data
        ));
        $this->data->save();
     }

    /**
     * transformData
     * Creating the final DB-ready json
     * --------------------------------------------------
     * @param array $histogramData
     * @return string (json)
     * --------------------------------------------------
     */
    public static final function transformData($histogramData) {
        $dbData = array(
            'datasets' => array(),
            'data'     => array()
        );

        $i = 0;
        foreach ($histogramData as $entry) {
            /* Creating the new entry */
            $newEntry = array();
            foreach ($entry as $key=>$value) {
                if ($key == 'date') {
                    /* In date */
                    $newEntry['date'] = $value;
                } else {
                    /* In dataset */
                    if ( ! array_key_exists($key, $dbData['datasets'])) {
                        $dbData['datasets'][$key] = 'data_' . $i++;
                    }
                    $dataSetKey = $dbData['datasets'][$key];
                    $newEntry[$dataSetKey] = $value;
                }
            }
            array_push($dbData['data'], $newEntry);
        }

        return json_encode($dbData);
    }


}