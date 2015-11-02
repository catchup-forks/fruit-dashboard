<?php

trait ChartWidgetTrait
{
    /* -- Settings -- */
    private static $chartSettings = array(
        'resolution' => array(
            'name'       => 'Time-scale',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days',
            'help_text'  => 'Set the timescale for the X axis of the chart.'
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'help_text'  => 'The name of the widget.'
        ),
        'length' => array(
            'name'       => 'Length',
            'type'       => 'INT',
            'validation' => 'required|min:2',
            'default'    => 10,
            'help_text'  => 'The number of data points on your widget.'
        ),
    );

    /* -- Choice functions -- */
    public function resolution()
    {
        return array(
            'days'   => 'Daily',
            'weeks'  => 'Weekly',
            'months' => 'Monthly',
            'years'  => 'Yearly'
        );
    }

    /**
     * getTemplateData
     * Return all values used in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateData()
    {
        return array_merge(parent::getTemplateData(), array(
            'data'          => $this->buildChartData(),
            'currentDiff'   => array(0),
            'currentValue'  => array(0),
            'format'        => $this->getFormat(),
        ));
    }

    /**
     * getChartJSData
     * Return template ready grouped dataset.
     * --------------------------------------------------
	 * @param array $data
     * @param string $dateFormat
     * @param boolean $cumulative
     * @return array
     * --------------------------------------------------
     */
    protected function getChartJSData($dateFormat, $cumulative=FALSE) {
        $dataSets = array(array(
            'type'   => 'bar',
            'color'  => SiteConstants::getChartJsColors()[0],
            'name'   => $this->getSettings()['name'],
            'values' => array()
        ));

        if ($cumulative) {
            array_push($dataSets, array(
                'type'   => 'line',
                'color'  => SiteConstants::getChartJsColors()[1],
                'name'   => 'Difference',
                'values' => array()
            ));
        }
        $datetimes = array();

        foreach ($this->buildHistogram() as $entry) {
            $value = $entry['value'];
            array_push($dataSets[0]['values'], $value);
            /* Getting the diff. */
            if ($cumulative) {
                if (isset($prevValue)) {
                    $diffedValue = $value - $prevValue;
                } else {
                    $diffedValue = 0;
                }
                array_push($dataSets[1]['values'], $diffedValue);
                $prevValue = $value;
            }
            array_push($datetimes, Carbon::createFromTimestamp($entry['timestamp'])->format($dateFormat));
        }
        $isCombined = $cumulative ? 'true': 'false'; 
        return array(
            'isCombined' => $isCombined,
            'datasets'   => $dataSets,
            'labels'     => $datetimes,
        );
    }
}
?>
