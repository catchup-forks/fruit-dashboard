<?php

trait MultipleHistogramChartLayoutTrait
{
    /* The maximum number of datasets on a multiple widget. */
    protected static $maxDataSets = 5;

    use HistogramChartLayoutTrait {
        HistogramChartLayoutTrait::getChartJSData as _getChartJSData;
    }

    /**
     * getChartJSData
     * Return template ready grouped dataset.
     * --------------------------------------------------
     * @param string $dateFormat
     * @return array
     * --------------------------------------------------
     */
    protected function getChartJSData($dateFormat)
    {
        if ($this->toSingle) {
            return $this->_getChartJSData($dateFormat);
        }

        /* Data init. */
        $datetimes = array();
        $dataSets = $this->initializeMultiDataSets();

        /* Data transform, to chartJS ready values. */
        foreach ($this->buildHistogram() as $entry) {

            /* Adding to datasets. */
            foreach (self::getEntryValues($entry) as $key=>$value) {
                array_push($dataSets[$key]['values'], $value);
            }

            /* Adding formatted datetimes. */
            array_push(
                $datetimes,
                Carbon::createFromTimestamp($entry['timestamp'])->format($dateFormat)
            );

        }

        return array(
            'isCombined'   => 'false',
            'datasets'     => self::removeEmptyDatasets($dataSets),
            'labels'       => $datetimes,
            'currentDiff'  => $this->compare(),
            'currentValue' => $this->getLatestValues()
        );
    }

    /**
     * initializeDataSets
     * Return the default arrays for chartJS data.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function initializeMultiDataSets()
    {
        $dataSets = array();
        $i = 0;
        $colors = SiteConstants::getChartJsColors();

        foreach ($this->dataSets as $name=>$id) {
            $dataSets[$id] = array(
                'type'   => 'line',
                'color'  => $colors[$i++%count($colors)],
                'name'   => $name,
                'values' => array()
            );
        }

        return $dataSets;
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
        $hasData = false;
        $cleanedDataSets = array();
        foreach ($datasets as $dataset) {
            if ((count($dataset['values']) > 0) && (max($dataset['values']) > 0)) {
                array_push($cleanedDataSets, $dataset);
            }
        }
        
        usort($cleanedDataSets, function($a, $b) {
            return array_sum($a['values']) < array_sum($b['values']);
        });

        return array_slice($cleanedDataSets, 0, static::$maxDataSets);
    }
}
