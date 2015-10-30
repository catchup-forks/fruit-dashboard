<?php

/* This class is responsible for multiple histogram data collection. */
abstract class MultipleHistogramDataCollector extends HistogramDataCollector
{
    /**
     * formatData
     * Formatting data to the multiple histogram format.
     * --------------------------------------------------
     * @param Carbon $date
     * @param mixed $data
     * @return array
     * --------------------------------------------------
     */
    protected function formatData($date, $data) {
        $dataSets = $this->getDataSets();
        $encodedData = array(
            'timestamp' => $date->getTimestamp()
        );
        foreach ($data as $key=>$value) {
            if ( ! array_key_exists($key, $dataSets)) {
                /* Key did not exist, adding to datasets. */
                $this->addToDataSets($key);
                $dataSets = $this->getDataSets();
            }
            if ( ! is_numeric($value)) {
                /* Value is not right for histograms, exiting. */
                return null;
            }
            $encodedData[$dataSets[$key]] = $value;
        }
        /* Populating zero values to keep integrity. */
        foreach ($dataSets as $dataset) {
            if ( ! in_array($dataset, array_keys($encodedData))) {
                $encodedData[$dataset] = 0;
            }
        }
        return $encodedData;
     }

    /**
     * getEntries
     * Return the entries
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function getEntries() {
        if ( (! is_array($this->data)) ||
                (! array_key_exists('data', $this->data))) {
            return array();
        }
        return $this->data['data'];
    }

    /**
     * getDataSets
     * Return the dataSets json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getDataSets() {
        if ( ( ! is_array($this->data)) ||
                ( ! array_key_exists('datasets', $this->data))) {
            return array();
        }
        return $this->data['datasets'];
    }

    /**
     * addToDataSets
     * Adding a new key to the datasets.
     * --------------------------------------------------
     * @param string $key
     * --------------------------------------------------
     */
    private function addToDataSets($key) {
        $dataSets = $this->getDataSets();
        if (empty($dataSets)) {
            /* Empty dataset */
           $this->data = array(
                'datasets' => array($key => 'data_0'),
                'data'     => array()
           );
       } else {
            /* Adding to datasets. */
           $name = 'data_' . count($dataSets);
           $dataSets[$key] = $name;

           /* Adding 0 to previous values. */
           $newData = array();
           foreach ($this->getEntries() as $entry) {
                $newEntry = $entry;
                $newEntry[$name] = 0;
                array_push($newData, $newEntry);
           }

           /* Creating layout. */
           $this->data = array(
                'datasets' => $dataSets,
                'data'     => $newData
           );
        }
       /* Saving to DB. */
        $this->save();
    }

    /**
     * save
     * Saving the data.
     * --------------------------------------------------
     * @param array $data
     * --------------------------------------------------
     */
    public function save($data=null) {
        if ( ! is_null($data) && ! array_key_exists('datasets', $data)) {
            $data = array(
                'data'     => $data,
                'datasets' => $this->getDataSets()
            );
        }
        parent::save($data);
    }
}
