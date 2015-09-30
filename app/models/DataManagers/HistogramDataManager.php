<?php

/* This class is responsible for histogram data collection. */
abstract class HistogramDataManager extends DataManager
{
    protected static $defaultEntries = 15;
    protected static $staticFields = array('date', 'timestamp');
    abstract public function getCurrentValue();

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
        $entryTime = self::getEntryTime($entry);
        $dbEntry = $this->formatData($entryTime, self::getEntryValues($entry));

        if (is_null($dbEntry)) {
            return;
        }

        /* Saving data only every 15 minutes. */
        $currentData = $this->sortHistogram(FALSE);

        if ( ! is_array($currentData) && $this->data->raw_value != 'loading') {
            /* Initializing data. */
            $this->initializeData();
        } else if (count($currentData) > 0) {
            if (Carbon::createFromTimestamp(end($currentData)['timestamp'])->diffInMinutes($entryTime) < 15) {
                array_pop($currentData);
            }
        }

        array_push($currentData, $dbEntry);
        $this->saveData($currentData);
    }

    /**
     * getLatestValues
     * Returning the last values in the histogram.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public function getLatestValues() {
        return self::getEntryValues($this->getLatestData());
     }

    /**
     * getHistogram
     * Returning the histogram, in chartJS ready format.
     * --------------------------------------------------
     * @param array $range
     * @param string $resolution
     * @param int ilength
     * @param bool diff
     * @return array
     * --------------------------------------------------
     */
    public function getHistogram($range, $resolution, $ilength=null, $diff=FALSE) {
        $length = is_null($ilength) ? static::$defaultEntries : $ilength;

        /* Calling proper method based on resolution. */
        switch ($resolution) {
            case 'hours':  $dateFormat = 'M-d h'; break;
            case 'days':   $dateFormat = 'M-d'; break;
            case 'weeks':  $dateFormat = 'Y-W'; break;
            case 'months': $dateFormat = 'Y-M'; break;
            case 'years':  $dateFormat = 'Y'; break;
            default:       $dateFormat = 'Y-m-d'; break;
        }
        return $this->getChartJSData($range, $resolution, $length, $diff, $dateFormat);
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
        $referenceTime = Carbon::createFromTimestamp($latestData['timestamp']);

        foreach ($this->sortHistogram() as $entry) {
            $entryTime = Carbon::createFromTimestamp($entry['timestamp']);

            /* Checking for a match. */
            if (static::matchesTime($referenceTime, $entryTime, $period, $multiplier)) {
                /* Creating an arrays that will hold the values. */
                $values = array();
                foreach (self::getEntryValues($entry) as $dataId=>$value) {
                    if (array_key_exists($dataId, $latestData)) {
                        $values[$dataId] = $latestData[$dataId] - $value;
                    }
                }
                return $values;
            }
        }

        /* No values found using last one */
        return self::getEntryValues($latestData);
    }

    /**
     * getChartJSData
     * Returning template ready grouped dataset.
     * --------------------------------------------------
     * @param array $range
     * @param string $resolution
     * @param int length
     * @param bool $diff
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
     */
    protected function getChartJSData($range, $resolution, $length, $diff , $dateFormat) {
        $datetimes = array();
        $dataSet = array(
            'color'  => SiteConstants::getChartJsColors()[0],
            'values' => array()
        );
        $histogram = $this->buildHistogram($range, $resolution, $length);
        if ($diff) {
            $histogram = self::getDiff($histogram);
        }
        foreach ($histogram as $entry) {
            array_push($datetimes, Carbon::createFromTimestamp($entry['timestamp'])->format($dateFormat));
            array_push($dataSet['values'], $entry['value']);
        }
        return array('datasets' => array($dataSet), 'labels' => $datetimes);
    }


    /**
     * buildHistogram
     * Returning the Histogram in the range,
     * --------------------------------------------------
     * @param array $range
     * @param string $resolution
     * @param int $length
     * @return array
     * --------------------------------------------------
    */
    protected function buildHistogram($range, $resolution, $length) {
        $recording = TRUE;
        $histogram = array();
        foreach ($this->sortHistogram() as $entry) {
            $entryTime = Carbon::createFromTimestamp($entry['timestamp']);

            /* Range conditions */
            if ( ! is_null($range)) {
                if (($entryTime >= $range['start']) && !$recording) {
                    /* Reached the start of the period -> start recording. */
                    $recording = TRUE;
                } else if (($entryTime <= $range['end']) && $recording) {
                    /* Reached the end of the period -> stop recording. */
                    break;
                }
            }

            if ($recording) {
                $push = FALSE;
                if ( ! isset($previousEntryTime)) {
                    /* First element always makes it to the final histogram. */
                    $push = TRUE;
                } else {
                    if (static::isBreakPoint($entryTime, $previousEntryTime, $resolution)) {
                        $push = TRUE;
                    }
                }

                if ($push) {
                    array_push($histogram, $entry);
                }

                if (count($histogram) >= $length) {
                    /* Enough data. */
                    break;
                }

                /* Saving previous entry time. */
                $previousEntryTime = $entryTime;
            }
        }

        return array_reverse($histogram);
    }

    /**
     * getDiff
     * Returning the differentiated values of an array.
     * --------------------------------------------------
     * @param array $data
     * @return array
     * --------------------------------------------------
     */
    protected static function getDiff(array $data, $dataName='value') {
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
     * sortHistogram
     * Sorting the array.
     * --------------------------------------------------
     * @param boolean $asc
     * @return array
     * --------------------------------------------------
     */
    protected function sortHistogram($asc=TRUE) {
        $fullHistogram = $this->getData();
        if (is_array($fullHistogram)) {
            usort($fullHistogram, array('HistogramDataManager', 'timestampSort'));
        } else {
            $fullHistogram = array();
        }
        return $asc ? $fullHistogram : array_reverse($fullHistogram);
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
     protected function getLatestData() {
        $histogram = $this->sortHistogram(FALSE);
        /* Handle empty data */
        if ($histogram == null) {
            return array();
        } else {
            return end($histogram);
        }
     }


    /**
     * getEntryValues
     * Returning only the values of the entry,
     * excluding staticFields.
     * --------------------------------------------------
     * @param array $entry
     * @return array
     * --------------------------------------------------
     */
    protected static final function getEntryValues($entry) {
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
     * matchesTime (compare())
     * Checks if the datetime should be used in compare.
     * --------------------------------------------------
     * @param Carbon $referenceTime
     * @param Carbon $entryTime
     * @param string $period
     * @param integer $multiplier
     * @return boolean
     * --------------------------------------------------
    */
    private static function matchesTime($referenceTime, $entryTime, $period, $multiplier) {
        if ($period == 'minutes') {
            return $entryTime->diffInMinutes($referenceTime) == $multiplier;
        } else if ($period == 'hours') {
            return $entryTime->diffInHours($referenceTime) == $multiplier;
        } else if ($period == 'days') {
            return $entryTime->diffInDays($referenceTime) == $multiplier;
        } else if ($period == 'weeks') {
            return $entryTime->diffInWeeks($referenceTime) == $multiplier;
        } else if ($period == 'months') {
            return $entryTime->diffInMonths($referenceTime) == $multiplier;
        } else if ($period == 'years') {
            return $entryTime->diffInYears($referenceTime) == $multiplier;
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
    private static function getEntryTime($entry) {
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
    private static function timestampSort($CseZso1, $CseZso2) {
        return $CseZso1['timestamp'] < $CseZso2['timestamp'];
    }
}