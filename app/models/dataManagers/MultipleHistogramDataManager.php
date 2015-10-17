<?php

/* This class is responsible for histogram data collection. */
abstract class MultipleHistogramDataManager extends HistogramDataManager
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
        if (empty($data)) {
            /* There is no data. */
            return null;
        }

        $dataSets = $this->getDataSets();
        $decodedData = array(
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
     * getEntries
     * Returning the entries
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getEntries() {
        if ( (! is_array($this->data)) ||
                (! array_key_exists('data', $this->data))) {
            return array();
        }
        return $this->data['data'];
    }

    /**
     * getDataSets
     * Returning the dataSets json decoded.
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
     * getChartJSData
     * Returning template ready grouped dataset.
     * --------------------------------------------------
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
     */
    protected function getChartJSData($dateFormat) {
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
        $histogram = $this->buildHistogram();
        if ($this->diff) {
            $histogram = self::getDiff($histogram);
        }
        foreach ($histogram as $entry) {
            array_push($datetimes, Carbon::createFromTimestamp($entry['timestamp'])->format($dateFormat));
            foreach (self::getEntryValues($entry) as $dataId => $value) {
                if (array_key_exists($dataId, $groupedData)) {
                    array_push($groupedData[$dataId]['values'], $value);
                }
            }
        }
        return array(
            'datasets' => array_values($groupedData),
            'labels' => $datetimes
        );
    }

    /**
     * transformData
     * Creating the final DB-ready json
     * --------------------------------------------------
     * @return string (json)
     * --------------------------------------------------
     */
    private static final function transformData() {
        $dbData = array(
            'datasets' => array(),
            'data'     => array()
        );

        /* Saving empty histogram. */
        if (empty($histogramData)) {
            return json_encode($dbData);
        }

        $i = 0;
        foreach ($this->data as $entry) {
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

        return $dbData;
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
     * saveDatasets
     * Saving data sets.
     * --------------------------------------------------
     * @param array $datasets
     * @param boolean $commit
     * --------------------------------------------------
     */
    public function saveDatasets(array $datasets, $commit=TRUE) {
        $this->data = array(
            'data'     => $this->getEntries(),
            'datasets' => $datasets
        );

        if ($commit) {
            $this->save();
        }
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