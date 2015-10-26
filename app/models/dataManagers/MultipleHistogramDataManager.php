<?php

/* This class is responsible for histogram data collection. */
abstract class MultipleHistogramDataManager extends HistogramDataManager
{
    /**
     * The number of top datasets shown.
     *
     * @var bool
     */
    private static $maxValues = 5;

    /**
     * Whether or not we should transform to single histogram.
     *
     * @var bool
     */
    protected $toSingle = FALSE;

    /**
     * setSingle
     * Sets the toSingle varibale.
     * --------------------------------------------------
     * @param boolean $single
     * --------------------------------------------------
     */
    public function setSingle($single) {
        $this->toSingle = $single;
    }

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
        if ($this->toSingle) {
            return parent::getChartJSData($dateFormat);
        }
        $groupedData = array();
        $datetimes = array();
        $i = 0;
        foreach ($this->getDataSets() as $name=>$dataId) {
            $groupedData[$dataId] = array(
                'type'   => 'line',
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
                    array_push($groupedData[$dataId]['values'], (int)$value);
                }
            }
        }
        return array(
            'isCombined' => false,
            'datasets'   => self::removeEmptyDatasets(array_values($groupedData)),
            'labels'     => $datetimes
        );
    }

    /**
     * transformData
     * Creating the final DB-ready json
     * --------------------------------------------------
     * @param array ($histogramData)
     * @return array
     * --------------------------------------------------
     */
    protected static final function transformData($histogramData) {
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

    /**
     * sortHistogram
     * Sorting the array, and summing the values.
     * --------------------------------------------------
     * @param boolean $desc
     * @return array
     * --------------------------------------------------
     */
    protected function sortHistogram($desc=TRUE) {
        if ($this->toSingle) {
            $histogram = array();
            /* Summarizing the entries. */
            foreach (parent::sortHistogram($desc) as $entry) {
                $newEntry = array(
                    'timestamp' => $entry['timestamp'],
                    'value'     => array_sum(static::getEntryValues($entry))
                );
                array_push($histogram, $newEntry);
            }
        } else {
            $histogram = parent::sortHistogram($desc);
        }
        /* Returning the parent by default. */
        return $histogram;
    }

    /**
     * removeEmptyDatasets
     * Returning the datasets, removing the empty ones.
     * --------------------------------------------------
     * @param array $datasets
     * @return array
     * --------------------------------------------------
     */
    private static function removeEmptyDatasets($datasets) {
        $hasData = FALSE;
        /* Removing empty datasets to boost performance of the sorting. */
        $cleanedDataSets = array();
        foreach ($datasets as $dataset) {
            if ((count($dataset['values']) > 0) && (max($dataset['values']) > 0)) {
                array_push($cleanedDataSets, $dataset);
            }
        }
        /* Sorting the array. */
        usort($cleanedDataSets, function ($a, $b) {
            return array_sum($a['values']) < array_sum($b['values']);
        });
    
        /* Returning the first N values. */
        return array_slice($cleanedDataSets, 0, self::$maxValues);
    }
    
    /**
     * compare
     * Comparing the current value respect to period.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function compare() {
        if ($this->toSingle) {
            return parent::compare();
        }
        $this->setSingle(TRUE);
        $values = parent::compare();
        $this->setSingle(FALSE);
        return $values;
    }

    /**
     * getLatestValues
     * Returns the current values in the dataset.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getLatestValues() {
        if ($this->toSingle) {
            return parent::getLatestValues();
        }
        $this->setSingle(TRUE);
        $this->setDiff(FALSE);
        $values = parent::getLatestValues();
        $this->setSingle(FALSE);
        return $values;
    }
}
