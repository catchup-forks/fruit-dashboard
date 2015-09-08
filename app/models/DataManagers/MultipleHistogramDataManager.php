<?php

/* This class is responsible for histogram data collection. */
abstract class MultipleHistogramDataManager extends HistogramDataManager
{

    protected static $staticFields = array('date', 'timestamp');

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
            'date'      => $date,
            'timestamp' => time()
        );
        foreach ($data as $key=>$value) {
            if ( is_null($dataSets) || ! array_key_exists($key, $dataSets)) {
                $this->addToDataSets($key);
                $dataSets = $this->getDataSets();
            }
            $decodedData[$dataSets[$key]] = $value;
        }

        return $decodedData;
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
     * addToDataSets
     * Adding a new key to the datasets.
     * --------------------------------------------------
     * @param string $key
     * --------------------------------------------------
     */
    public function addToDataSets($key) {
        $dataSets = $this->getDataSets();
        if (is_null($dataSets)) {
           $this->data->raw_value = json_encode(array(
                'datasets' => array($key => 'data_0'),
                'data'     => array()
           ));
       } else {
           $dataSets[$key] = 'data_' . count($dataSets);
           $this->data->raw_value = json_encode(array(
                'datasets' => $dataSets,
                'data'     => $this->getData()
           ));
        }
       $this->data->save();
    }

    /**
     * groupDataSets
     * Returning template ready grouped dataset.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function groupDataSets() {
        $groupedData = array();
        $i = 0;
        foreach ($this->getDataSets() as $name=>$dataId) {
            $groupedData[$dataId] = array(
                'name'  => $name,
                'color' => SiteConstants::getChartJsColors()[$i++],
                'data'  => array()
            );
        }
        foreach ($this->getData() as $oneValues) {
            foreach ($oneValues as $dataId => $value) {
                if ( ! in_array($dataId, static::$staticFields)) {
                    array_push($groupedData[$dataId]['data'], $value);
                }
            }
        }

        return $groupedData;
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
                if (in_array($key, static::$staticFields)) {
                    /* In date */
                    $newEntry[$key] = $value;
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