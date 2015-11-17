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
}
?>
