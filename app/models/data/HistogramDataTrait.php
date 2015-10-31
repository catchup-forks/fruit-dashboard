<?php

trait HistogramDataTrait
{
    protected static $staticFields = array('date', 'timestamp');

    /**
     * getDiff
     * Return the differentiated values of an array.
     * --------------------------------------------------
     * @param array $data
     * @return array
     * --------------------------------------------------
     */
    protected static function getDiff(array $data)
    {
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
     * Return only the values of the entry,
     * excluding staticFields.
     * --------------------------------------------------
     * @param array $entry
     * @return array
     * --------------------------------------------------
     */
    protected static final function getEntryValues($entry)
    {
        if ( ! is_array($entry)) {
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
     * Return the time from an entry
     * --------------------------------------------------
     * @param array/float $entry
     * @return Carbon
     * --------------------------------------------------
     */
    protected static function getEntryTime($entry)
    {
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
    protected static function timestampSort($CseZso1, $CseZso2)
    {
        return $CseZso1['timestamp'] < $CseZso2['timestamp'];
    }

	/**
     * sortHistogram
     * Sorting the array.
     * --------------------------------------------------
	 * @param array $entries
     * @param boolean $desc
     * @return array
     * --------------------------------------------------
     */
    protected static function sortHistogram(array $entries, $desc=TRUE) {
        if (is_array($entries)) {
            usort($entries, array('self', 'timestampSort'));
        } else {
            $entries = array();
        }
        return $desc ? $entries : array_reverse($entries);
    }

}
?>
