<?php

/* This class is responsible for histogram data collection. */
abstract class HistogramDataManager extends DataManager
{
    protected static $defaultEntries = 15;
    protected static $staticFields = array('date', 'timestamp');
    abstract public function getCurrentValue();

    /**
     * initializeData
     * --------------------------------------------------
     * First time population of the data.
     * --------------------------------------------------
     */
    public function initializeData() {
        $this->saveData(array());
        $this->collectData();
    }

    /**
     * collectData
     * --------------------------------------------------
     * Getting the new value based on getCurrentValue()
     * --------------------------------------------------
     */
    public function collectData($options=array()) {
        /* Getting the entry */
        $entry = array_key_exists('entry', $options) ? $options['entry'] : $this->getCurrentValue();

        /* Getting db ready entry and entryTime */
        $entryTime = static::getEntryTime($entry);
        $dbEntry = $this->formatData($entryTime, static::getEntryValues($entry));

        if (is_null($dbEntry)) {
            return;
        }

        /* Saving data only every 15 minutes. */
        $currentData = $this->getData();

        if ( ! is_array($currentData) && $this->data->raw_value != 'loading') {
            /* Initializing data. */
            $this->initializeData();
        } else if (count($currentData) > 0) {
            if (Carbon::createFromTimestamp(end($currentData)['timestamp'])->diffInMinutes(Carbon::now()) < 15) {
                array_pop($currentData);
            }
        }

        array_push($currentData, $dbEntry);
        $this->saveData($currentData);
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
        if ( ! is_numeric($data)) {
            return null;
        }
        return array('value' => $data, 'timestamp' => $date->getTimestamp());
     }

    /**
     * getLatestData
     * Returning the last data in the histogram.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public function getLatestData() {
        $histogram = $this->getData();
        /* Handle empty data */
        if ($histogram == null) {
            return array();
        } else {
            return $this->getEntryValues(end($histogram));
        }
     }

    /**
     * getHistogram
     * Returning the histogram.
     * --------------------------------------------------
     * @param array $range
     * @param string $resolution
     * @param int ilength
     * @return array
     * --------------------------------------------------
     */
    public function getHistogram($range, $resolution, $ilength=null) {
        $length = is_null($ilength) ? static::$defaultEntries : $ilength;

        /* Calling proper method based on resolution. */
        switch ($resolution) {
            case 'minutes':  return $this->buildHistogram($range, $resolution, $length, 'h:i'); break;
            case 'hours':  return $this->buildHistogram($range, $resolution, $length, 'M-d h'); break;
            case 'days':   return $this->buildHistogram($range, $resolution, $length, 'M-d'); break;
            case 'weeks':  return $this->buildHistogram($range, $resolution, $length, 'W'); break;
            case 'months': return $this->buildHistogram($range, $resolution, $length, 'Y-M'); break;
            case 'years':  return $this->buildHistogram($range, $resolution, $length, 'Y'); break;
            default: return $this->buildHistogram($range, $resolution, $length, 'd'); break;
        }
    }

    /**
     * buildHistogram
     * Returning the Histogram in the range,
     * --------------------------------------------------
     * @param array $range
     * @param string $resolution
     * @param int $length
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
    */
    protected function buildHistogram($range, $resolution, $length, $dateFormat='Y-m-d') {
        /* Getting recorded histogram sorted by timestamp. */
        $fullHistogram = $this->sortHistogram();
        if ( ! is_null($fullHistogram)) {
            $last = end($fullHistogram);
        }

        /* If there's range, using reader. */
        $recording = TRUE;
        $histogram = array();

        foreach ($fullHistogram as $entry) {
            $entryTime = Carbon::createFromTimestamp($entry['timestamp']);
            /* Range conditions */
            if ( ! is_null($range)) {
                if (($entryTime >= $range['start']) && !$recording) {
                    /* Reached the start of the period -> start recording. */
                    $recording = TRUE;
                } else if (($entryTime <= $range['end']) && $recording) {
                    /* Reached the end of the period -> stop recording. */
                    return array_reverse($histogram);
                }
            }
            if ($recording) {
                /* Frequency conditions. */
                if (isset($previousEntry)) {
                    $previousEntryTime = Carbon::createFromTimestamp($previousEntry['timestamp']);
                    if (static::isBreakPoint($entryTime, $previousEntryTime, $resolution)) {
                        /* Passing new element to the array. */
                        $previousEntry['datetime'] = $previousEntryTime->format($dateFormat);
                        array_push($histogram, $previousEntry);
                        if ($entry == $last) {
                            /* There's only one element in the dataset. */
                            $entry['datetime'] = $entryTime->format($dateFormat);
                            array_push($histogram, $entry);
                        }
                    }
                } else if ($entry == $last) {
                    /* There's only one element in the dataset. */
                    $entry['datetime'] = $entryTime->format($dateFormat);
                    array_push($histogram, $entry);
                }

                /* Saving previous entry. */
                $previousEntry = $entry;
            }

            if (count($histogram) >= $length) {
                /* Enough data. */
                return array_reverse($histogram);
            }
        }

        return array_reverse($histogram);
    }

    /**
     * isBreakPoint
     * Checks if the entry is a breakpoint in the histogram.
     * --------------------------------------------------
     * @param Carbon $entryTime
     * @param Carbon $previousEntryTime
     * @param string $resolution
     * @return boolean
     * --------------------------------------------------
    */
    private static function isBreakPoint($entryTime, $previousEntryTime, $resolution) {
        if ($resolution == 'minutes') {
            return $entryTime->format('Y-m-d h:i') !== $previousEntryTime->format('Y-m-d h:i');
        } else if ($resolution == 'hours') {
            return $entryTime->format('Y-m-d h') !== $previousEntryTime->format('Y-m-d h');
        } else if ($resolution == 'days') {
            return ! $entryTime->isSameDay($previousEntryTime);
        } else if ($resolution == 'weeks') {
            return $entryTime->format('Y-W') !== $previousEntryTime->format('Y-W');
        } else if ($resolution == 'months') {
            return $entryTime->format('Y-m') !== $previousEntryTime->format('Y-m');
        } else if ($resolution == 'years') {
            return $entryTime->format('Y') !== $previousEntryTime->format('Y');
        }
        return FALSE;
    }

    /**
     * getDiff
     * Returning the differentiated values of an array.
     * --------------------------------------------------
     * @param array $data
     * @return array
     * --------------------------------------------------
     */
    public static final function getDiff(array $data, $dataName='value') {
        $differentiatedArray = array();
        foreach ($data as $entry) {
            /* Copying entry. */
            $diffEntry = $entry;
            $diffValue = 0;
            if (isset($lastValue)) {
                $diffValue = $entry[$dataName] - $lastValue;
            }
            $diffEntry[$dataName] = $diffValue;
            array_push($differentiatedArray, $diffEntry);
            $lastValue = $entry[$dataName];
        }
        return $differentiatedArray;
    }

    /**
     * compare
     * Comparing the current value respect to period.
     * --------------------------------------------------
     * @param string $period
     * @param int $multiplier
     * @return array
     * --------------------------------------------------
     */
    public function compare($period, $multiplier=1) {
        $latestData = $this->getLatestData();

        foreach ($this->sortHistogram() as $entry) {
            $entryTime = Carbon::createFromTimestamp($entry['timestamp']);

            /* Checking for a match. */
            if (static::matchesTime($entryTime, $period, $multiplier)) {
                /* Creating an arrays that will hold the values. */
                $values = array();
                foreach ($this->getEntryValues($entry) as $dataId=>$value) {
                    if (array_key_exists($dataId, $latestData)) {
                        $values[$dataId] = $latestData[$dataId] - $value;
                    }
                }
                return $values;
            }
        }

        /* No values found using last one */
        return $this->getEntryValues($latestData);
    }

    /**
     * sortHistogram
     * Sorting the array.
     * --------------------------------------------------
     * @param boolean $asc
     * @return array
     * --------------------------------------------------
     */
    private function sortHistogram($asc=TRUE) {
        $fullHistogram = $this->getData();
        if ($fullHistogram != null) {
            usort($fullHistogram, array('HistogramDataManager', 'timestampSort'));
        } else {
            $fullHistogram = array();
        }
        return $asc ? $fullHistogram : array_reverse($fullHistogram);
    }

    /**
     * getEntryValues (buildHistogram)
     * Returning only the values of the entry,
     * excluding staticFields.
     * --------------------------------------------------
     * @param array $entry
     * @return array
     * --------------------------------------------------
     */
    private static final function getEntryValues($entry) {
        if (! is_array($entry)) {
            return $entry;
        }
        $values = array();
        foreach ($entry as $key=>$value) {
            if ( ! in_array($key, static::$staticFields)) {
                $values[$key] = $value;
            }
        }
        return $values;
    }

    /**
     * OBSOLETE getAverageValues (buildHistogram)
     * Merging multiple entries into one, by avereging the values.
     * --------------------------------------------------
     * @param array $entries
     * @return array ($entry)
     * --------------------------------------------------
     */
    private static final function _getAverageValues($entries) {
        $finalEntry = array();
        /* Summarizing all data into one array. */
        foreach ($entries as $entry) {
            foreach ($entry as $key=>$value) {
                if ( ! in_array($key, static::$staticFields)) {
                    if ( ! array_key_exists($key, $finalEntry)) {
                        $finalEntry[$key] = $value;
                    } else {
                        $finalEntry[$key] += $value;
                    }
                }
            }
        }

        /* Averaging the values */
        if (count($entries) > 0) {
            foreach (array_keys($finalEntry) as $key) {
               $finalEntry[$key] /= count($entries);
            }
        }
        return $finalEntry;
    }

    /**
     * matchesTime (compare())
     * Checks if the datetime should be used in compare.
     * (Carbon extension)
     * --------------------------------------------------
     * @param Carbon $entryTime
     * @param string $period
     * @param integer $multiplier
     * @return boolean
     * --------------------------------------------------
    */
    private static function matchesTime($entryTime, $period, $multiplier) {
        $now = Carbon::now();
        if ($period == 'minutes') {
            return $entryTime->diffInMinutes($now) == $multiplier;
        } else if ($period == 'hours') {
            return $entryTime->diffInHours($now) == $multiplier;
        } else if ($period == 'days') {
            return $entryTime->diffInDays($now) == $multiplier;
        } else if ($period == 'weeks') {
            return $entryTime->diffInWeeks($now) == $multiplier;
        } else if ($period == 'months') {
            return $entryTime->diffInMonths($now) == $multiplier;
        } else if ($period == 'years') {
            return $entryTime->diffInYears($now) == $multiplier;
        }
        return FALSE;
    }

    /**
     * getEntryTime
     * returning the time from an entry  (collectData())
     * --------------------------------------------------
     * @param array/float $entry
     * @return Carbon
     * --------------------------------------------------
     */
    private static final function getEntryTime($entry) {
        if ( ! is_array($entry)) {
            return Carbon::now();
        }
        if (array_key_exists('timestamp', $entry)) {
            try {
                return Carbon::createFromTimestamp($entry['timestamp']);
            } catch (Exception $e) {
                return Carbon::now();
            }
        } else if (array_key_exists('date', $entry)) {
            try {
                return Carbon::createFromFormat('Y-m-d', $entry['date']);
            } catch (Exception $e) {
                return Carbon::now();
            }
        }
        return Carbon::now();
    }

    /**
     * timestampSort
     * Comparing two timestamps. (buildHistogram())
     * --------------------------------------------------
     * @param array $CseZso1
     * @param array $CseZso2
     * @return boolean
     * --------------------------------------------------
     */
    private static final function timestampSort($CseZso1, $CseZso2) {
        return $CseZso1['timestamp'] < $CseZso2['timestamp'];
    }
}