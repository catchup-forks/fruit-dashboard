<?php

trait MultipleHistogramWidgetTrait
{
    use HistogramWidgetTrait {
        HistogramWidgetTrait::setActiveHistogram as _setActiveHistogram;
    }

    /**
     * Whether or not summing the values.
     *
     * @var bool
     */
    protected $toSingle = false;

    /**
     * The currently active histogram datasets.
     *
     * @var array
     */
    protected $dataSets = array();

    /**
     * setSingle
     * Set the toSingle varibale.
     * --------------------------------------------------
     * @param boolean $single
     * --------------------------------------------------
     */
    public function setSingle($single)
    {
        $this->toSingle = $single;
    }

    /**
     * setActiveHistogram
     * Set the active histogram.
     * --------------------------------------------------
     * @param bool $dirty
     * @throws WidgetException
     * --------------------------------------------------
     */
    protected function setActiveHistogram($histogram)
    {
        if ($this->toSingle) {
            return $this->_setActiveHistogram($histogram);
        }

        if ( ! array_key_exists('datasets', $histogram) ||
             ! array_key_exists('data', $histogram)) {
            throw new WidgetException('data, or dataset not found.');
        }

        $this->activeHistogram = $histogram['data'];

        $this->dataSets = $histogram['datasets'];

        $this->setDirty(true);
    }

    /**
     * transformToSingle
     * Summarize the values to create a single histogram.
     * --------------------------------------------------
     * @param array $entries
     * @return array
     * --------------------------------------------------
     */
    protected static function transformToSingle(array $entries)
    {
        $histogram = array();
        foreach ($entries as $entry) {
            $newEntry = array(
                'timestamp' => $entry['timestamp'],
                'value'     => array_sum(static::getEntryValues($entry))
            );
            array_push($histogram, $newEntry);
        }
        return $histogram;
    }

    /**
     * transformDatasets
     * Return the datasets as an array, where keys are
     * datasets.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function transformeDatasets()
    {
        /* Initializing transformed data sets. */
        $transformedDatasets = array();
        foreach ($this->dataSets as $dataset=>$id) {
            $transformedDatasets[$id] = array();
        }

        /* Populating the array. */
        foreach ($this->activeHistogram as $entry) {
            foreach ($entry as $key=>$value) {

                /* Omitting static fields. */
                if (in_array($key, static::$staticFields)) {
                    continue;
                }

                if (array_key_exists($key, $transformedDatasets)) {
                    array_push($transformedDatasets[$key], $value);
                }

            }
        }

        return $transformedDatasets;
    }

    /**
     * getDatasetName
     * Return the dataset name based on id.
     * --------------------------------------------------
     * @param string $key
     * @return array
     * --------------------------------------------------
     */
    protected function getDatasetName($key)
    {
        return array_flip($this->dataSets)[$key];
    }

    /**
     * filterDatasets
     * Returning the non-empty datasets, max=n.
     * --------------------------------------------------
     * @param int $n
     * @return array
     * --------------------------------------------------
     */
    protected function filterDatasets($n=5)
    {
        /* Creating data, value pairs. */
        $filteredDatasets = array();
        foreach ($this->transformeDatasets() as $dataset=>$values) {

            $sum = array_sum($values);

            if ($sum > 0) {
                $filteredDatasets[$dataset] = $sum;
            }
        }

        /* Sorting the array revers. */
        arsort($filteredDatasets);

        /* Selecting the top results. */
        return array_keys(array_slice($filteredDatasets, 0, $n));
    }
}
?>
