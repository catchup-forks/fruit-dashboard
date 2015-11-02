<?php

trait MultipleChartWidgetTrait
{
    use ChartWidgetTrait;

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
     * removeEmptyDatasets
     * Return the datasets, removing the empty ones.
     * --------------------------------------------------
     * @param array $datasets
     * @return array
     * --------------------------------------------------
     */
    private static function removeEmptyDatasets($datasets)
    {
        $hasData = FALSE;
        $cleanedDataSets = array();
        foreach ($datasets as $dataset) {
            if ((count($dataset['values']) > 0) && (max($dataset['values']) > 0)) {
                array_push($cleanedDataSets, $dataset);
            }
        }
        return $cleanedDataSets;
    }
}
?>
