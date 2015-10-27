<?php

trait HistogramChartLayoutTrait
{

    /**
     * getChartData
     * Returning the chart data.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    protected function getChartData(array $options) {

        /* Setting options. */
        if (array_key_exists('range', $options)) {
            $this->dataManager->setRange($options['range']);
        }
        if (array_key_exists('length', $options)) {
            $this->dataManager->setLength($options['length']);
        }
        if (array_key_exists('resolution', $options)) {
            $this->dataManager->setResolution($options['resolution']);
        }

        return $this->dataManager->getHistogram();
    }

    /**
     * getChartTemplateData
     * Returning all values that are used in templates.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    protected function getChartTemplateData() 
    {
        /* Chart specific data. */
        return array(
            'currentDiff'   => $this->getDiff(),
            'currentValue'  => $this->getLatestValues(),
            'hasCumulative' => $this->hasCumulative()
        );
    }

    /**
     * getChartTemplateMeta
     * Returning the url, and selector.
     * --------------------------------------------------
     * @param array $meta
     * @return array
     * --------------------------------------------------
     */
    protected function getChartTemplateMeta($meta) 
    {
        /* Chart specific data. */
        $meta['urls']['statUrl'] = route('widget.singlestat', $this->id);
        $meta['selectors']['graph'] = '[id^=chart-container]';
        return $meta;
    }

    /**
     * setupChartDataManager
     * Setting up the datamanager
     * --------------------------------------------------
     * @param DataManager $manager
     * @return DataManager
     * --------------------------------------------------
     */
    protected function setupChartDataManager($manager) 
    {
        //$manager->setDiff(TRUE);
    }
}
