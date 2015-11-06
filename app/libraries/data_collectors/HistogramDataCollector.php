<?php

/* This class is responsible for histogram data collection. */
abstract class HistogramDataCollector extends DataCollector
{
    use HistogramDataTrait;

    abstract public function getCurrentValue();

    /**
     * isCumulative
     * Returns whether or not the data is cumulative.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
    protected function isCumulative() {
        if ( ! array_key_exists('cumulative', $this->descriptorAttributes)) {
            return FALSE;
        }
        return $this->descriptorAttributes['cumulative'];
    }

    /**
     * collect
     * --------------------------------------------------
     * Getting the new value based on getCurrentValue()
     * @throws ServiceException
     * --------------------------------------------------
     */
    public function collect($options=array())
    {
        /* Getting the entry */
        $entry = array_key_exists('entry', $options) ? $options['entry'] : $this->getCurrentValue();

        /* Getting db ready entry and entryTime */
        $entryTime = self::getEntryTime($entry);
        $dbEntry = $this->formatData($entryTime, self::getEntryValues($entry));

        if (is_null($dbEntry)) {
            return;
        }

        $currentData = $this->sortHistogram($this->getEntries(), FALSE);
        $lastData = end($currentData);

        /* Checking for cumulative. */
        if ( ! empty($lastData)) {
            if ($this->isCumulative() &&
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
     * formatData
     * Formatting the data to DB ready format.
     * --------------------------------------------------
     * @param Carbon $date
     * @param mixed $data
     * @return array
     * --------------------------------------------------
     */
    protected function formatData($date, $data)
    {
        if (is_array($data) && ! empty($data)) {
            $data = array_values($data)[0];
        }

        if ( ! is_numeric($data)) {
            return null;
        }

        return array('value' => $data, 'timestamp' => $date->getTimestamp());
     }

    /**
     * getEntries
     * Return the entries
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function getEntries() {
        return $this->data;
    }

}
