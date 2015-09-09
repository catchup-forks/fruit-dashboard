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
        if ($today->diffInHours($this->last_updated) >= $this->update_period) {
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
        return array('date' => $date->toDateString(),'value' => $data, 'timestamp' => $date->getTimestamp());
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
        $lastEntry = end($histogram);
        return $lastEntry;
     }

    /**
     * getHistogram
     * Returning the histogram.
     * --------------------------------------------------
     * @param array $range
     * @param string $frequency
     * @return array
     * --------------------------------------------------
     */
    public function getHistogram($range, $frequency) {
        /* Calling proper method based on frequency. */
        switch ($frequency) {
            case 'minutely':  return $this->buildHistogram($range, $frequency, 'h:i'); break;
            case 'hourly':  return $this->buildHistogram($range, $frequency, 'm'); break;
            case 'daily':   return $this->buildHistogram($range, $frequency, 'M-d'); break;
            case 'weekly':  return $this->buildHistogram($range, $frequency, 'W'); break;
            case 'monthly': return $this->buildHistogram($range, $frequency, 'Y-M'); break;
            case 'yearly':  return $this->buildHistogram($range, $frequency, 'Y'); break;
            default: return $this->buildHistogram($range, $frequency, 'd'); break;
        }

        /* Default return. */
        return $this->getData();
    }

    /**
     * buildHistogram
     * Returning the Histogram in the range,
     * --------------------------------------------------
     * @param array $range
     * @param string $frequency
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
    */
    protected function buildHistogram($range, $frequency, $dateFormat='Y-m-d') {
        /* Getting recorded histogram sorted by timestamp. */
        $fullHistogram = $this->getData();
        usort($fullHistogram, array('HistogramDataManager', 'timestampSort'));

        /* If there's range, using reader. */
        $recording = TRUE;
        $histogram = array();
        $first = TRUE;
        $sampleEntries = array();
        $last = end($fullHistogram);

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
                if (( ! $first && $this->isBreakPoint($entryTime, $previousEntryTime, $frequency)) || ($entry == $last)) {
                    if ($entry == $last) {
                        array_push($sampleEntries, $entry);
                    }
                    /* Passing new element to the array. */
                    $newEntry = static::getAverageValues($sampleEntries);
                    $newEntry['date'] = $entryTime->format($dateFormat);
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
     * @param string $frequency
     * @return boolean
     * --------------------------------------------------
    */
    protected function isBreakPoint($entryTime, $previousEntryTime, $frequency) {
        if ($frequency == 'minutely') {
            return $entryTime->format('Y-m-d h:i') !== $previousEntryTime->format('Y-m-d h:i');
        } else if ($frequency == 'hourly') {
            return $entryTime->format('Y-m-d h') !== $previousEntryTime->format('Y-m-d h');
        } else if ($frequency == 'daily') {
            return ! $entryTime->isSameDay($previousEntryTime);
        } else if ($frequency == 'weekly') {
            return $entryTime->format('Y-W') !== $previousEntryTime->format('Y-W');
        } else if ($frequency == 'monthly') {
            return $entryTime->format('Y-m') !== $previousEntryTime->format('Y-m');
        } else if ($frequency == 'yearly') {
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
    public static final function getDiff($data, $dataName='value') {
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
     * getAverageValues
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
     * Comparing two timestamps.
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