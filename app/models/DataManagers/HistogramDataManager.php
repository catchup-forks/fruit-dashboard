<?php

/* This class is responsible for histogram data collection. */
abstract class HistogramDataManager extends DataManager
{
    protected static $entries = 15;
    protected static $staticFields = array('date', 'timestamp');
    abstract public function getCurrentValue();

    /**
     * initializeData
     * --------------------------------------------------
     * First time population of the data.
     * --------------------------------------------------
     */
    public function initializeData() {
        $this->collectData();
    }

    /**
     * collectData
     * --------------------------------------------------
     * Getting the new value based on getCurrentValue()
     * --------------------------------------------------
     */
    public function collectData() {
        /* Calculating current value */
        $newData = $this->getCurrentValue();
        $today = Carbon::now();

        /* Getting previous values. */
        $currentData = $this->getData();
        if ( ! empty($currentData)) {
            $lastData = end($currentData);
        } else {
            $currentData = array();
            $lastData = null;
        }

        /* If popping the old value. */
        if ($today->diffInSeconds($this->last_updated) >= $this->update_period) {
            /* Updating last updated. */
            $this->last_updated = $today;
            $this->save();
        } else if ($lastData) {
            array_pop($currentData);
        }

        /* Adding, saving data. */
        array_push($currentData, $this->formatData($today, $newData));
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
     * @return array
     * --------------------------------------------------
     */
    public function getHistogram($range, $resolution) {
        /* Calling proper method based on resolution. */
        return $this->buildHistogram($range, 'minutely', 'h:i');
        switch ($resolution) {
            case 'minutely':  return $this->buildHistogram($range, $resolution, 'h:i'); break;
            case 'hourly':  return $this->buildHistogram($range, $resolution, 'M-d h'); break;
            case 'daily':   return $this->buildHistogram($range, $resolution, 'M-d'); break;
            case 'weekly':  return $this->buildHistogram($range, $resolution, 'W'); break;
            case 'monthly': return $this->buildHistogram($range, $resolution, 'Y-M'); break;
            case 'yearly':  return $this->buildHistogram($range, $resolution, 'Y'); break;
            default: return $this->buildHistogram($range, $resolution, 'd'); break;
        }
    }

    /**
     * buildHistogram
     * Returning the Histogram in the range,
     * --------------------------------------------------
     * @param array $range
     * @param string $resolution
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
    */
    protected function buildHistogram($range, $resolution, $dateFormat='Y-m-d') {
        /* Getting recorded histogram sorted by timestamp. */
        $fullHistogram = $this->sortHistogram();
        if ( ! is_null($fullHistogram)) {
            $last = end($fullHistogram);
        }

        /* If there's range, using reader. */
        $recording = TRUE;
        $histogram = array();
        $first = TRUE;
        $sampleEntries = array();
        $previousEntryTime = Carbon::now();

        foreach ($fullHistogram as $entry) {
            $entryTime = Carbon::createFromTimestamp($entry['timestamp']);
            /* Range conditions */
            if ( ! is_null($range)) {
                if (($entryTime < $range['stop']) && !$recording) {
                    /* Reached the end of the period -> start recording. */
                    $recording = TRUE;
                } else if (($entryTime < $range['start']) && $recording) {
                    /* Reached the start of the period -> stop recording. */
                    return array_reverse($histogram);
                }
            }
            if ($recording) {
                /* Frequency conditions. */
                if ($first || $entry == $last) {
                    array_push($sampleEntries, $entry);
                }
                if (static::isBreakPoint($entryTime, $previousEntryTime, $resolution) || ($entry == $last)) {
                    /* Passing new element to the array. */
                    $newEntry = static::getAverageValues($sampleEntries);
                    $newEntry['datetime'] = $entryTime->format($dateFormat);
                    array_push($histogram, $newEntry);
                    $sampleEntries = array();
                }

                /* Adding to samples. */
                array_push($sampleEntries, $entry);

                /* Saving previous time. */
                $previousEntryTime = $entryTime;

                if ($first) {
                    $first = FALSE;
                }
            }

            if (count($histogram) >= static::$entries) {
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
        if ($resolution == 'minutely') {
            return $entryTime->format('Y-m-d h:i') !== $previousEntryTime->format('Y-m-d h:i');
        } else if ($resolution == 'hourly') {
            return $entryTime->format('Y-m-d h') !== $previousEntryTime->format('Y-m-d h');
        } else if ($resolution == 'daily') {
            return ! $entryTime->isSameDay($previousEntryTime);
        } else if ($resolution == 'weekly') {
            return $entryTime->format('Y-W') !== $previousEntryTime->format('Y-W');
        } else if ($resolution == 'monthly') {
            return $entryTime->format('Y-m') !== $previousEntryTime->format('Y-m');
        } else if ($resolution == 'yearly') {
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
     * getEntryValues (buildHistogram)
     * Returning only the values of the entry,
     * excluding staticFields.
     * --------------------------------------------------
     * @param array $entriy
     * @return array
     * --------------------------------------------------
     */
    private static final function getEntryValues($entry) {
        $values = array();
        foreach ($entry as $key=>$value) {
            if ( ! in_array($key, static::$staticFields)) {
                $values[$key] = $value;
            }
        }
        return $values;
    }



    /**
     * getAverageValues (buildHistogram)
     * Merging multiple entries into one, by avereging the values.
     * --------------------------------------------------
     * @param array $entries
     * @return array ($entry)
     * --------------------------------------------------
     */
    private static final function getAverageValues($entries) {
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