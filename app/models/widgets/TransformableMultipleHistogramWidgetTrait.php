<?php

trait TransformableMultipleHistogramWidgetTrait
{
    /* -- Choice functions -- */
    public function type() {
        $types = array(
            'multiple' => 'By source',
            'chart'    => 'Single line',
            'table'    => 'Table'
        );
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
        } else {
            $manager->setDiff(TRUE);
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
