<?php

/* This class is responsible for histogram data collection. */
abstract class MultipleHistogramDataManager extends HistogramDataManager
{
    /**
     * initializeData
     * --------------------------------------------------
     * First time population of the data.
     * --------------------------------------------------
     */
    public function initializeData() {
        $this->saveData(array($this->getCurrentValue()), TRUE);
    }

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
        $dataSets = $this->getDataSets();
        $decodedData = array(
            'timestamp' => $date->getTimestamp()
        );
        foreach ($data as $key=>$value) {
            if (!array_key_exists($key, $dataSets)) {
                $this->addToDataSets($key);
                $dataSets = $this->getDataSets();
            }
            $decodedData[$dataSets[$key]] = $value;
        }

        return $decodedData;
     }

    /**
     * getDataScheme
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public static function getDataScheme() {
        return array('datasets', 'data');
    }

    /**
     * getData
     * Returning the raw data json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getData() {
        $data = json_decode($this->data->raw_value, 1);
        if ( ! array_key_exists('data', $data)) {
            return array();
        }
        return $data['data'];
    }

    /**
     * getDataSets
     * Returning the dataSets json decoded.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getDataSets() {
        $data = json_decode($this->data->raw_value, 1);
        if ( ! array_key_exists('datasets', $data)) {
            return array();
        }
        return $data['datasets'];
    }

    /**
     * buildHistogram
     * Returning template ready grouped dataset.
     * --------------------------------------------------
     * @param array $range
     * @param string $resolution
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
     */
    public function buildHistogram($range, $resolution, $dateFormat='Y-m-d') {
        $groupedData = array();
        $datetimes = array();
        $i = 0;
        foreach ($this->getDataSets() as $name=>$dataId) {
            $groupedData[$dataId] = array(
                'name'   => $name,
                'color'  => SiteConstants::getChartJsColors()[($i++) % count(SiteConstants::getChartJsColors())],
                'values' => array()
            );
        }
        foreach (parent::buildHistogram($range, $resolution, $dateFormat) as $oneValues) {
            array_push($datetimes, $oneValues['datetime']);
            foreach ($oneValues as $dataId => $value) {
                if ( ! in_array($dataId, static::$staticFields)) {
                    if (array_key_exists($dataId, $groupedData)) {
                        array_push($groupedData[$dataId]['values'], $value);
                    }
                }
            }
        }

        return array('datasets' => array_values($groupedData), 'datetimes' => $datetimes);
    }

    /**
     * saveData
     * Saving the data to DB
     * --------------------------------------------------
     * @param data $inputData
     * @param boolean $transform
     * --------------------------------------------------
     */
     public function saveData($inputData, $transform=FALSE) {
        if ($transform) {
            $this->data->raw_value = self::transformData($inputData);
        } else {
            /* Getting dataSets */
            $this->data->raw_value = json_encode(array(
                'datasets' => $this->getDataSets(),
                'data'     => $inputData
            ));
        }
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
    public static final function transformData(array $histogramData) {
        $dbData = array(
            'datasets' => array(),
            'data'     => array()
        );

        /* Saving empty histogram. */
        if (empty($histogramData)) {
            return json_encode($dbData);
        }

        $i = 0;
        foreach ($histogramData as $entry) {
            /* Creating the new entry */
            $newEntry = array();

            /* Iterating through the entry. */
            foreach ($entry as $key=>$value) {
                if (in_array($key, static::$staticFields)) {
                    /* In static fields */
                    $newEntry[$key] = $value;
                } else {
                    /* In dataset */
                    if ( ! array_key_exists($key, $dbData['datasets'])) {
                        $dbData['datasets'][$key] = 'data_' . $i++;
                    }
                    $dataSetKey = $dbData['datasets'][$key];
                    $newEntry[$dataSetKey] = is_numeric($value) ? $value : 0;
                }
            }
            /* Final integrity check. */
            if ( ! array_key_exists('timestamp', $newEntry)) {
                $newEntry['timestamp'] = time();
            }

            array_push($dbData['data'], $newEntry);
        }

        return json_encode($dbData);
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
        if (is_null($dataSets)) {
            /* Empty dataset */
           $this->data->raw_value = json_encode(array(
                'datasets' => array($key => 'data_0'),
                'data'     => array()
           ));
       } else {
            /* Adding to datasets. */
           $name = 'data_' . count($dataSets);
           $dataSets[$key] = $name;

           /* Adding 0 to previous values. */
           $newData = array();
           foreach ($this->getData() as $entry) {
                $newEntry = $entry;
                $newEntry[$name] = 0;
                array_push($newData, $newEntry);
           }

           /* Creating layout. */
           $this->data->raw_value = json_encode(array(
                'datasets' => $dataSets,
                'data'     => $newData
           ));
        }

       /* Saving to DB. */
        $this->data->save();
    }

}