<?php

/* This class is responsible for multiple histogram data collection. */
abstract class MultipleHistogramDataCollector extends HistogramDataCollector
{
    const OTHERSNAME    = 'others';
    const STARTOTHERSAT = 40;

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
        $others = 0;
        $encodedData = array(
            'timestamp' => $date->getTimestamp()
        );

        foreach ($data as $key=>$value) {
            if ( ! array_key_exists($key, $dataSets)) {
                /* Key did not exist, adding to datasets. */
                if (count($dataSets) > self::STARTOTHERSAT) {
                    /* We're not storing over n datasets, sorry about that. */
                    $key = self::OTHERSNAME;
                    $others += $value;
                }

                /* Adding others if did not exist. */
                $this->addToDataSets($key);
                $dataSets = $this->getDataSets();
            }

            if ( ! is_numeric($value)) {
                /* Value is not right for histograms. */
                $value = 0;
            }

            if ($key != self::OTHERSNAME) {
                $encodedData[$dataSets[$key]] = $value;
            }
        }

        if (array_key_exists(self::OTHERSNAME, $dataSets)) {
            $encodedData[$dataSets[self::OTHERSNAME]] = $others;
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
            /* Checking for existence. */
            if (array_key_exists($key, $dataSets)) {
                return;
            }
            
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

	/**
     * transformData
     * Creating the final DB-ready json
     * --------------------------------------------------
     * @param array ($histogramData)
     * @return array
     * --------------------------------------------------
     */
    protected static function transformData($histogramData) {
        $dbData = array(
            'datasets' => array(),
            'data'     => array()
        );
        /* Saving empty histogram. */
        if (empty($histogramData)) {
            return json_encode($dbData);
        }
        $i = 0;
        $others = 0;
        foreach ($histogramData as $entry) {
            /* Creating the new entry */
            $newEntry = array();
            /* Iterating through the entry. */
            foreach ($entry as $key=>$value) {
                if (in_array($key, static::$staticFields)) {
                    /* In static fields */
                    $newEntry[$key] = $value;
                } else {
                    /* Cleaning value. */
                    $value = is_numeric($value) ? $value : 0;

                    /* In dataset */
                    if ( ! array_key_exists($key, $dbData['datasets'])) {
                        if (count($dbData['datasets']) > self::STARTOTHERSAT) {
                            /* We're not storing over n datasets, sorry about that. */
                            $key = self::OTHERSNAME;
                            $others += $value;
                        }
                    
                        if ( ! array_key_exists($key, $dbData['datasets'])) {
                            $dbData['datasets'][$key] = 'data_' . $i++;
                        }
                    }
                    
                    if ($key != self::OTHERSNAME) {
                        Log::info(count($dbData['datasets']));
                        $newEntry[$dbData['datasets'][$key]] = $value;
                    }
                }
            }

            if (array_key_exists(self::OTHERSNAME, $dbData['datasets'])) {
                $newEntry[$dbData['datasets'][self::OTHERSNAME]] = $value;
            }

            /* Final integrity check. */
            if ( ! array_key_exists('timestamp', $newEntry)) {
                $newEntry['timestamp'] = time();
            }
            array_push($dbData['data'], $newEntry);
        }
        return $dbData;
    }

}
