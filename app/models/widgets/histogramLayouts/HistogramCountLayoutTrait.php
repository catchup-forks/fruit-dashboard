<?php

trait HistogramCountLayoutTrait
{
    /**
     * getCountData
     * Returning the count data.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    protected function getCountData(array $options)
    {

        /* Setting options. */
        if (array_key_exists('range', $options)) {
            $this->dataManager->setRange($options['range']);
        }
        if (array_key_exists('resolution', $options)) {
            $this->dataManager->setResolution($options['resolution']);
        }

        return $this->dataManager->build();
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
     * Returning all values that are used in templates.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function getCountTemplateData() 
    {
        $countTemplateData = array(
            'description'  => '',
            'startDate'    => $this->getStartDate(),
            'currentDiff'  => $this->getDiff(),
            'currentValue' => $this->getLatestValues()
        );
        if ($this instanceof iServiceWidget) {
            $countTemplateData['footer'] = $this->getServiceSpecificName();
        } else {
            $countTemplateData['footer'] = '';
        }
        return $countTemplateData;
    }

    /**
     * getCountTemplateMeta
     * Returning the selector.
     * --------------------------------------------------
     * @param array $meta
     * @return array
     * --------------------------------------------------
     */
    protected function getCountTemplateMeta($meta) 
    {
        /* Chart specific data. */
        $meta['selectors']['count'] = 'count-' . $this->id;
        return $meta;
    }

    /**
     * setupCountDataManager
     * Setting up the datamanager
     * --------------------------------------------------
     * @param DataManager $manager
     * @return DataManager
     * --------------------------------------------------
     */
    protected function setupCountDataManager($manager) 
    {
    }

}
