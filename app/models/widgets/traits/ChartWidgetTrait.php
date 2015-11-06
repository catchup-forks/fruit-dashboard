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
            'data'          => $this->getChartJSData('Y-m-d', TRUE),
            'currentDiff'   => array(0),
            'currentValue'  => array(0),
            'format'        => $this->getFormat(),
        ));
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
        foreach ($this->builgHistogram() as $entry) {
            /* Adding value */
            $value = $entry['value'];
            array_push($dataSets[0]['values'], $value);

            /* Adding diff. */
            if (isset($prevValue)) {
                $diffedValue = $value - $prevValue;
            } else {
                $diffedValue = 0;
            }
            array_push($dataSets[1]['values'], $diffedValue);

            /* Adding formatted datetimes. */
            array_push(
                $datetimes,
                Carbon::createFromTimestamp($entry['timestamp'])->format($dateFormat)
            );

            /* Saving value. */
            $prevValue = $value;
        }

        return array(
            'isCombined' => true,
            'datasets'   => $dataSets,
            'labels'     => $datetimes,
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
        return array(
            array(
                'type'   => 'line',
                'color'  => SiteConstants::getChartJsColors()[0],
                'name'   => $this->getDescriptor()->name,
                'values' => array()
            ),
            array(
                'type'   => 'bar',
                'color'  => SiteConstants::getChartJsColors()[1],
                'name'   => 'Difference',
                'values' => array()
            )
        );
    }

    /**
     * getDateFormat
     * Return the dateFormat, based on resolution.
     * --------------------------------------------------
     * @param string $resolution
     * @return array
     * --------------------------------------------------
     */
    protected function dateFormat($resolution=null)
    {
        if (is_null($resolution)) {
            $resolution = $this->getResolution();
        }

        switch ($resolution) {
            case 'hours':  return 'M-d h';
            case 'days':   return 'M-d';
            case 'weeks':  return 'Y-W';
            case 'months': return 'Y-M';
            case 'years':  return 'Y';
            default:       return 'Y-m-d';
        }
    }
}
?>
