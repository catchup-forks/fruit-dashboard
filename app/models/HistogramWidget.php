<?php

abstract class HistogramWidget extends DataWidget implements iCronWidget
{
    const ENTRIES = 15;

    /* -- Settings -- */
    public static $settingsFields = array(
        'frequency' => array(
            'name'       => 'Frequency',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily'
        ),
    );

    /* The settings to setup in the setup-wizard.*/
    public static $setupSettings = array();

    /* -- Choice functions -- */
    public function frequency() {
        return array(
            'daily'   => 'Daily',
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            'yearly'  => 'Yearly'
        );
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
    */
    abstract public function getCurrentValue();

    /**
     * collectData
     * --------------------------------------------------
     * Getting the new value based on getCurrentValue()
     * --------------------------------------------------
     */
    public function collectData() {
        try {
            /* Calculating current value */
            $newValue = $this->getCurrentValue();
        } catch (ServiceNotConnected $e) {
            return;
        }

        /* Getting previous values. */
        $currentData = json_decode($this->data->raw_value, 1);
        $lastData = end($currentData);
        $today = Carbon::now()->toDateString();

        /* If today, popping the old value. */
        if ($lastData && ($lastData['date'] == $today)) {
            array_pop($currentData);
        }

        /* Adding, saving data. */
        array_push($currentData, array('date' => $today, 'value' => $newValue));
        $this->data->raw_value = json_encode($currentData);
        $this->data->save();
    }

    /**
     * getLatestData
     * --------------------------------------------------
     * Returning the last data in the histogram.
     * @return float
     * --------------------------------------------------
     */
     public function getLatestData() {
        $histogram = $this->getData();
        $lastEntry = end($histogram);
        return $lastEntry;
     }

    /**
     * getFirstData
     * --------------------------------------------------
     * Returning the first data in the histogram.
     * @return float
     * --------------------------------------------------
     */
     public function getFirstData() {
        $histogram = $this->getData();
        return $histogram['value'];
     }

    /**
     * getData
     * --------------------------------------------------
     * Returning the histogram.
     * @param array $postData
     * @return array
     * --------------------------------------------------
     */
    public function getData($postData=null) {
        /* Getting range if present. */
        if (isset($postData['range'])) {
            $range = $postData['range'];
        } else {
            $range = null;
        }

        /* Looking for forced frequency. */
        if (isset($postData['frequency'])) {
            $frequency = $postData['frequency'];
        } else {
            $frequency = $this->getSettings()['frequency'];
        }

        /* Calling proper method based on frequency. */
        switch ($frequency) {
            case 'daily':   return $this->getHistogram($range, $frequency, 'd'); break;
            case 'weekly':  return $this->getHistogram($range, $frequency, 'W'); break;
            case 'monthly': return $this->getHistogram($range, $frequency, 'M'); break;
            case 'yearly':  return $this->getHistogram($range, $frequency, 'Y'); break;
            default: break;
        }

        /* Default return. */
        return json_decode($this->data->raw_value, 1);
    }

    /**
     * getHistogram
     * Returning the Histogram in the range,
     * --------------------------------------------------
     * @param array $range
     * @param string $frequency
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
    */
    protected function getHistogram($range, $frequency, $dateFormat='Y-m-d') {
        /* Getting recorded histogram reversed. */
        $reversedHistogram = array_reverse(json_decode($this->data->raw_value, 1));

        /* If there's range, using reader. */
        $recording = TRUE;
        $histogram = array();
        $first = TRUE;

        foreach ($reversedHistogram as $entry) {
            /* Range conditions */
            if ( ! is_null($range)) {
                if (($entry['date'] < $range['stop']) && !$recording) {
                    /* Reached the end of the period -> start recording. */
                    $recording = TRUE;
                } else if (($entry['date'] < $range['start']) && $recording) {
                    /* Reached the start of the period -> stop recording. */
                    return array_reverse($histogram);
                }
            }

            if ($recording) {
                /* Frequency conditions. */
                $entryDate = Carbon::createFromFormat('Y-m-d', $entry['date']);
                if ( ! $this->useEntryInHistogram($entryDate, $frequency) && ! $first) {
                    continue;
                }

                /* First data is always in the histogram. */
                if ($first) {
                    $first = FALSE;
                }
                /* Saving data with custom date format. */
                array_push($histogram, array(
                    'value' => $entry['value'],
                    'date'  => $entryDate->format($dateFormat)
                ));
            }

            if (count($histogram) >= self::ENTRIES) {
                /* Enough data. */
                return array_reverse($histogram);
            }
        }

        return array_reverse($histogram);
    }

    /**
     * useEntryInHistogram
     * Whether or not use the specific entry in the
     * histomgram.
     * --------------------------------------------------
     * @param Carbon $entryDate
     * @param string $frequency
     * @return bool
     * --------------------------------------------------
    */
    protected function useEntryInHistogram($entryDate, $frequency) {
        if (is_null($frequency)) {
            $frequency = $this->getSettings()['frequency'];
        }
        if ($frequency == 'daily') {
            return TRUE;
        } else if ($frequency == 'weekly') {
            if ($entryDate->format('D') == 'Mon') {
                return TRUE;
            }
        } else if ($frequency == 'monthly') {
            if ($entryDate->format('d') == '31') {
                return TRUE;
            }
        } else if ($frequency == 'yearly') {
             if ($entryDate->format('m-d') == '12-31') {
                return TRUE;
            }
        }
        return FALSE;
    }
}
?>
