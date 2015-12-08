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
            return false;
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

        $currentData = $this->sortHistogram($this->getEntries(), false);
        $lastData = end($currentData);

        if ( ! empty($lastData)) {
            /* Updating last data. */
            if (Carbon::createFromTimestamp($lastData['timestamp'])->isSameDay($entryTime)) {
                /* Forcing daily data collection, could go to data descriptors later. */
                array_pop($currentData);
            }
        }

        if ( ! empty($currentData) && $this->isCumulative()) {
            /* Applying cumulativeness. */
            $lastData = end($currentData);
            foreach (self::getEntryValues($dbEntry) as $key=>$value) {
                if (array_key_exists($key, $lastData)) {
                    $dbEntry[$key] += $lastData[$key];
                }
            }
        }

        if (self::getEntryValues($dbEntry) != false) {
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
