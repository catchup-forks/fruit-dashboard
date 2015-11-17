<?php

trait HistogramCountLayoutTrait
{
    abstract protected function getCountDescription();
    abstract protected function getCountFooter();

    /**
     * getCountData
     * Return the count data.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    protected function getCountData(array $options)
    {

        /* Setting options. */
        if (array_key_exists('range', $options)) {
            $this->setRange($options['range']);
        }
        if (array_key_exists('resolution', $options)) {
            $this->setResolution($options['resolution']);
        }

        return $this->buildHistogram();
    }

    /**
     * getStartDate
     * --------------------------------------------------
     * Returns the start date based on settings.
     * @return array
     * --------------------------------------------------
     */
    protected function getStartDate()
    {
        $multiplier = $this->getSettings()['length'];
        $now = Carbon::now();
        switch ($this->getSettings()['resolution']) {
            case 'hours' : return $now->subHours($multiplier)->format('H:i');
            case 'days'  : return $now->subDays($multiplier)->format('l (m.d)');
            case 'weeks' : return $now->subWeeks($multiplier)->format('Y.m.d');
            case 'months': return $now->subMonths($multiplier)->format('F, Y');
            default: return '';
        }
    }

    /**
     * getCountTemplateData
     * Return all values that are used in templates.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function getCountTemplateData()
    {
        $countTemplateData = array(
            'description'  => $this->getCountDescription(),
            'footer'       => $this->getCountFooter(),
            'startDate'    => $this->getStartDate(),
            'currentDiff'  => $this->compare(),
            'currentValue' => $this->getLatestValues()
        );

        return $countTemplateData;
    }
}
