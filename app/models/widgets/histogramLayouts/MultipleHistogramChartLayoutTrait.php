<?php

trait MultipleHistogramChartLayoutTrait
{
    use HistogramChartLayoutTrait {
        HistogramChartLayoutTrait::getChartJSData as _getChartJSData;
        HistogramChartLayoutTrait::initializeDataSets as _initializeDataSets;
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
        /* Data init. */
        $datetimes = array();
        $dataSets = $this->initializeDataSets();

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
            'datasets'     => $dataSets,
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
    protected function initializeDataSets()
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
}
