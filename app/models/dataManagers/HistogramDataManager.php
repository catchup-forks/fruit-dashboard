<?php

/* This class is responsible for histogram data collection. */
abstract class HistogramDataManager extends DataManager
{
    protected static $cumulative = FALSE;
    protected static $staticFields = array('date', 'timestamp');
    abstract public function getCurrentValue();

    /**
     * Whether or not using a diffed values.
     *
     * @var bool
     */
    protected $diff = FALSE;

    /**
     * The chart's resolution.
     *
     * @var string
     */
    protected $resolution = 'days';

    /**
     * The chart's range
     *
     * @var array
     */
    protected $range = null;

    /**
     * The length of the chart
     *
     * @var int
     */
    protected $length = 15;

    /**
     * hasCumulative
     * Returns whether or not the data has a cumulative option.
     */
    public function hasCumulative() {
        return static::$cumulative;
    }

    /**
     * setDiff
     * Setting the $diff
     * --------------------------------------------------
     * @param bool $diff
     * --------------------------------------------------
     */
    public function setDiff($diff) {
        $this->diff = $diff;
    }

    /**
     * setRange
     * Setting the $range
     * --------------------------------------------------
     * @param array $range
     * --------------------------------------------------
     */
    public function setRange(array $range) {
        $this->range = $range;
    }

    /**
     * setResolution
     * Setting the $resolution
     * --------------------------------------------------
     * @param string $resolution
     * --------------------------------------------------
     */
    public function setResolution($resolution) {
        $this->resolution = $resolution;
    }

    /**
     * setLength
     * Setting the $length
     * --------------------------------------------------
     * @param int $length
     * --------------------------------------------------
     */
    public function setLength($length) {
        $this->length = $length;
    }

    /**
     * collect
     * --------------------------------------------------
     * Getting the new value based on getCurrentValue()
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function collect($options=array()) {
        /* Getting the entry */
        $entry = array_key_exists('entry', $options) ? $options['entry'] : $this->getCurrentValue();

        /* Getting db ready entry and entryTime */
        $entryTime = self::getEntryTime($entry);
        $dbEntry = $this->formatData($entryTime, self::getEntryValues($entry));

        if (is_null($dbEntry)) {
            return;
        }

        $currentData = $this->sortHistogram(FALSE);
        $lastData = $this->getLatestData();

        /* Checking for cumulative. */
        if ( ! empty($lastData)) {
            if (static::$cumulative &&
                    array_key_exists('sum', $options) &&
                    $options['sum'] == TRUE) {
                foreach (self::getEntryValues($dbEntry) as $key=>$value) {
                    if (array_key_exists($key, $lastData)) {
                        $dbEntry[$key] += $lastData[$key];
                    }
                }
            }
            /* Saving data only every 15 minutes. */
            if (Carbon::createFromTimestamp($lastData['timestamp'])->diffInMinutes($entryTime) < 15) {
                array_pop($currentData);
            }
        }
        if (self::getEntryValues($dbEntry) != FALSE) {
            array_push($currentData, $dbEntry);
            $this->save($currentData);
        }
    }

    /**
     * cleanupData
     * Removing hourly data if more than a week passed.
     * --------------------------------------------------
     * WARNING THIS FUNCTION DELETES DATA FROM THE DATASET
     * ALL HOURLY DATA THAT IS OVER TWO WEEKS OLD WILL BE
     *            D  E  S  T  R  O  Y  E  E  D
     * @return int number of deletions.
     * --------------------------------------------------
     */
     public function cleanupData() {
        $lastKept = null;
        $newData = array();
        $deleted = 0;
        foreach ($this->sortHistogram() as $entry) {
            /* Getting the time. */
            $entryTime = self::getEntryTime($entry);

            /* Checking the diff. */
            if (SiteConstants::cleanupPolicy($entryTime)) {
                /* Less is fresher than 2 weeks. */
                array_push($newData, $entry);
                continue;
            }

            if (is_null($lastKept) || ! $lastKept->isSameDay($entryTime)) {
                /* Keeping this data. (latest on the day) */
                $lastKept = $entryTime;
                array_push($newData, $entry);
            } else {
                $deleted++;
            }
        }

        /* Just making sure all went fine, and not deleting everything. */
        if (count($newData) > 0) {
            /* Overwriting the data, just to be nice sorting it ascending. */
            $this->save(array_reverse($newData));
        }

        return $deleted;
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
     * @return array
     * --------------------------------------------------
     */
    public function getHistogram() {
        /* Calling proper method based on resolution. */
        switch ($this->resolution) {
            case 'hours':  $dateFormat = 'M-d h'; break;
            case 'days':   $dateFormat = 'M-d'; break;
            case 'weeks':  $dateFormat = 'Y-W'; break;
            case 'months': $dateFormat = 'Y-M'; break;
            case 'years':  $dateFormat = 'Y'; break;
            default:       $dateFormat = 'Y-m-d'; break;
        }
        return $this->getChartJSData($dateFormat);
    }

    /**
     * compare
     * Comparing the current value respect to period.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function compare() {
        $histogram = $this->buildHistogram();
        if ($this->diff) {
            $histogram = self::getDiff($histogram);
        }
        
        if (count($histogram) < 1) {
            throw new WidgetException('Data not found');
        }

        $start = $histogram[0];
        $today = end($histogram);

        /* Creating an arrays that will hold the values. */
        $values = array();
        foreach (self::getEntryValues($start) as $dataId=>$value) {
            if (array_key_exists($dataId, $today)) {
                $values[$dataId] = $today[$dataId] - $value;
            }
        }

        return $values;
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
        $dataSets = array(array(
            'type'   => 'line',
            'color'  => SiteConstants::getChartJsColors()[0],
            'name'   => 'Sum',
            'values' => array()
        ));
        if ($this->hasCumulative()) {
            array_push($dataSets, array(
                'type'   => 'bar',
                'color'  => SiteConstants::getChartJsColors()[1],
                'name'   => 'Diff',
                'values' => array()
            ));
        }
        $datetimes = array();
        $histogram = $this->buildHistogram();
        if ($this->diff) {
            $histogram = self::getDiff($histogram);
        }
        foreach ($histogram as $entry) {
            $value = $entry['value'];
            array_push($dataSets[0]['values'], $value);

            /* Getting the diff. */
            if ($this->hasCumulative()) {
                if (isset($prevValue)) {
                    $diffedValue = $value - $prevValue;
                } else {
                    $diffedValue = 0;
                }
                array_push($dataSets[1]['values'], $diffedValue);
                $prevValue = $value;
            }

            array_push($datetimes, Carbon::createFromTimestamp($entry['timestamp'])->format($dateFormat));
        }
        $isCombined = $this->hasCumulative() ? 'true' : 'false' ; 
        return array(
            'isCombined' => $isCombined,
            'datasets'   => $dataSets,
            'labels'     => $datetimes,
        );
    }


    /**
     * buildHistogram
     * Returning the Histogram in the range,
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    protected function buildHistogram() {
        $recording = empty($this->range) ? TRUE : FALSE;
        $histogram = array();
        foreach ($this->sortHistogram() as $entry) {
            $entryTime = self::getEntryTime($entry);
            /* Range conditions */
            if ( ! empty($this->range)) {
                if (($entryTime <= $this->range['end']) && !$recording) {
                    /* Reached the start of the period -> start recording. */
                    $recording = TRUE;
                } else if (($entryTime <= $this->range['start']) && $recording) {
                    /* Reached the end of the period -> stop recording. */
                    break;
                }
            }

            if ($recording) {
                $push = FALSE;
                if ( ! isset($previousEntryTime)) {
                    /* First element always makes it to the final histogram. */
                    $push = TRUE;
                } else if ($this->isBreakPoint($entryTime, $previousEntryTime)){
                    $push = TRUE;
                }

                if ($push) {
                    array_push($histogram, $entry);
                }

                if (count($histogram) >= $this->length) {
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
     * getEntries
     * Returning the histogram entries.
     */
    public function getEntries() {
        return $this->data;
    }

    /**
     * sortHistogram
     * Sorting the array.
     * --------------------------------------------------
     * @param boolean $desc
     * @return array
     * --------------------------------------------------
     */
    protected function sortHistogram($desc=TRUE) {
        $fullHistogram = $this->getEntries();
        if (is_array($fullHistogram)) {
            usort($fullHistogram, array('HistogramDataManager', 'timestampSort'));
        } else {
            $fullHistogram = array();
        }
        return $desc ? $fullHistogram : array_reverse($fullHistogram);
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
        if (is_array($data) && ! empty($data)) {
            $data = array_values($data)[0];
        }
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
        if ($this->diff) {
            $histogram = self::getDiff($histogram);
        }
        /* Handle empty data */
        if (empty($histogram)) {
            return array();
        } else {
            return end($histogram);
        }
     }

    /**
     * isBreakPoint
     * Checks if the entry is a breakpoint in the histogram.
     * --------------------------------------------------
     * @param Carbon $entryTime
     * @param Carbon $previousEntryTime
     * @return boolean
     * --------------------------------------------------
    */
    private function isBreakPoint($entryTime, $previousEntryTime) {
        if ($this->resolution == 'minutes') {
            return $entryTime->format('Y-m-d h:i') !== $previousEntryTime->format('Y-m-d h:i');
        } else if ($this->resolution == 'hours') {
            return $entryTime->format('Y-m-d h') !== $previousEntryTime->format('Y-m-d h');
        } else if ($this->resolution == 'days') {
            return ! $entryTime->isSameDay($previousEntryTime);
        } else if ($this->resolution == 'weeks') {
            return $entryTime->format('Y-W') !== $previousEntryTime->format('Y-W');
        } else if ($this->resolution == 'months') {
            return $entryTime->format('Y-m') !== $previousEntryTime->format('Y-m');
        } else if ($this->resolution == 'years') {
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
    protected static function getDiff(array $data) {
        $differentiatedArray = array();
        foreach ($data as $entry) {
            /* Copying entry. */
            $diffValue = 0;
            if (isset($lastEntry)) {
                $diffEntry = array(
                    'timestamp' => $entry['timestamp']
                );
                foreach (self::getEntryValues($entry) as $key=>$value) {
                    if (array_key_exists($key, $lastEntry)) {
                        $diffEntry[$key] = $entry[$key] - $lastEntry[$key];
                    } 
                }
                array_push($differentiatedArray, $diffEntry);
            }
            $lastEntry = $entry;
        }
        if (count($differentiatedArray) <= 0) {
            return array($lastEntry);
        }
        return $differentiatedArray;

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
        $values = $entry;
        foreach ($entry as $key=>$value) {
            if (in_array($key, static::$staticFields)) {
                unset($values[$key]);
            }
        }
        return $values;
    }

    /**
     * getEntryTime
     * returning the time from an entry
     * --------------------------------------------------
     * @param array/float $entry
     * @return Carbon
     * --------------------------------------------------
     */
    protected static function getEntryTime($entry) {
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
