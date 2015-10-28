<?php

trait TransformableMultipleHistogramWidgetTrait
{
    /* -- Choice functions -- */
    public function type() {
        $types = parent::type();
        $types = array_merge(parent::type(), array(
            'multiple' => 'Multiple line chart by source',
            'chart'    => 'Cumulative difference chart',
            'table'    => 'Table layout'
        ));
        return $types;
    }

    /**
     * setupDataManager
     * Setting up the datamanager
     * --------------------------------------------------
     * @param array $options
     * @return DataManager
     * --------------------------------------------------
     */
    public function setupDataManager(array $options=array()) {
        $manager = parent::setupDataManager($options);
        /* Setting single. */
        if ($this->isCumulative()) {
            $manager->setSingle(TRUE);
        }
        return $manager;
    }

    
    /**
     * isCumulative
     * Returns whether or not the widget is cumulative.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
    private function isCumulative() {
        return $this->getSettings()['type'] != 'multiple';
    }

}
?>
