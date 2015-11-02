<?php

trait HistogramWidgetTrait
{
    use HistogramDataTrait;

    /**
     * Whether or not the increasing value means good.
     *
     * @var bool
     */
    protected static $isHigherGood = TRUE;

    /**
     * Whether or not using a diffed values.
     *
     * @var bool
     */
    protected $diff = FALSE;

    /**
     * The currently active histogram.
     *
     * @var array
     */
    protected $activeHistogram = array();

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
     * Whether or not the value has changed since last update.
     *
     * @var bool
     */
    protected $dirty = TRUE;

    /**
     * Cache
     *
     * @var array
     */
    protected $cache = array();

    /**
     * setDirty
     * Set the dirty bit.
     * --------------------------------------------------
     * @param bool $dirty
     * --------------------------------------------------
     */
    protected function setDirty($dirty)
    {
        $this->dirty = $dirty;
    }

    /**
     * setActiveHistogram
     * Set the active histogram.
     * --------------------------------------------------
     * @param bool $dirty
     * --------------------------------------------------
     */
    protected function setActiveHistogram($histogram)
    {
        $this->activeHistogram = $histogram;

        $this->setDirty(TRUE);
    }

    /**
     * setDiff
     * Setting the $diff
     * --------------------------------------------------
     * @param bool $diff
     * --------------------------------------------------
     */
    public function setDiff($diff)
    {
        if ($diff != $this->diff) {
            $this->setDirty(TRUE);
        }

        $this->diff = $diff;

        if ($diff) {
            $this->length += 1;
        }

    }

    /**
     * setRange
     * Setting the $range
     * --------------------------------------------------
     * @param array $range
     * --------------------------------------------------
     */
    public function setRange(array $range)
    {
        if ($range != $this->range) {
            $this->setDirty(TRUE);
        }

        $this->range = $range;
    }

    /**
     * setResolution
     * Setting the $resolution
     * --------------------------------------------------
     * @param string $resolution
     * --------------------------------------------------
     */
    public function setResolution($resolution)
    {
        if ($resolution != $this->resolution) {
            $this->setDirty(TRUE);
        }

        $this->resolution = $resolution;
    }

    /**
     * setLength
     * Setting the $length
     * --------------------------------------------------
     * @param int $length
     * --------------------------------------------------
     */
    public function setLength($length)
    {
        if ($length != $this->length) {
            $this->setDirty(TRUE);
        }

        $this->length = $length;
    }

    /**
     * compare
     * Compare the current value respect to period.
     * --------------------------------------------------
     * @param int $count
     * @return array
     * --------------------------------------------------
     */
    protected function compare($count)
    {
        $start = $this->getValueAt($count);
        $today = $this->getLatestValues();

        /* Creating an array that will hold the values. */
        $values = array();
        foreach (self::getEntryValues($start) as $dataId=>$value) {
            if (array_key_exists($dataId, $today)) {
                $values[$dataId] = $today[$dataId] - $value;
            }
        }

        return $values;
    }

    /**
     * getValueAt
     * Return the value at the count resolution.
     * --------------------------------------------------
     * @param int $count
     * @return array
     * --------------------------------------------------
     */
    private function getValueAt($count)
    {
        /* Saving length. */
        $origLength = $this->length;

        if ($count >= $this->length) {
            /* Not in range. */
            $this->setLength($count);
            $histogram = $this->buildHistogram();
            $index = 0;
        } else {
            $histogram = $this->buildHistogram();
            $index = $this->length - $count;
        }

        if ($index >= count($histogram)) {
            throw new WidgetException('Data not found');
        }

        /* Reset length. */
        $this->setLength($count);
        return $histogram[$index];
    }

    /**
     * buildHistogram
     * Return the Histogram in the range,
     * --------------------------------------------------
     * @param array $entries
     * @return array
     * --------------------------------------------------
    */
    public function buildHistogram()
    {
        /* Using cache. */
        if ( ! $this->dirty) {
            return $this->cache;
        }

        $recording = empty($this->range) ? TRUE : FALSE;
        $histogram = array();

        foreach (self::sortHistogram($this->activeHistogram) as $entry) {
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

        $histogram = array_reverse($histogram);

        if ($this->diff) {
            $histogram = self::getDiff($histogram);
        }
        
        /* Setting cache, resetting length. */
        $this->cache = $histogram;
        $this->setDirty(FALSE);
        $this->setLength(count($histogram));

        return $histogram;
    }

    /**
     * isBreakPoint
     * Check if the entry is a breakpoint in the histogram.
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
     * getLatestValues
     * Return the latest values in the histogram.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function getLatestValues()
    {
        $histogram = $this->buildHistogram();

        if (empty($histogram)) {
            return array();
        }

        return end($histogram);
    }


    /**
     * getHistory
     * Returning the historical data compared to the latest.
     * --------------------------------------------------
     * @param int $multiplier
     * @param string $resolution
     * @return array
     * --------------------------------------------------
     */
    public function getHistory($multiplier=1, $resolution=null) {

        try {
            $percent = ($currentValue / $value - 1) * 100;
        } catch (Exception $e) {
            $percent = 'inf';
        }
        return array(
            'value'   => $value,
            'percent' => $percent,
            'success' => $this->isSuccess($percent)
        );
    }

    /**
     * isSuccess
     * Returns whether or not the value is considered
     * good in the histogram
     * --------------------------------------------------
     * @param numeric $value
     * @return boolean
     * --------------------------------------------------
     */
    public static function isSuccess($value)
    {
        return  ($value < 0) xor static::$isHigherGood;
    }

}
?>
