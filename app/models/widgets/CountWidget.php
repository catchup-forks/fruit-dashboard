<?php

abstract class CountWidget extends Widget implements iAjaxWidget
{
    protected static $histogramDescriptor = '';

    /* -- Settings -- */
    public static $settingsFields = array(
        'period' => array(
            'name'       => 'Period',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days'
        ),
        'multiplier' => array(
            'name'       => 'Number of periods',
            'type'       => 'INT',
            'validation' => 'required',
            'default'    => '1'
        ),
    );

    /* -- Choice functions -- */
    public function period() {
        return array(
            'hours'  => 'Hour',
            'days'   => 'Day',
            'weeks'  => 'Week',
            'months' => 'Month',
            'years'  => 'Year'
        );
    }

    /**
     * getDataManager
     * Returning the corresponding DataManager
     * --------------------------------------------------
     * @return DataManager
     * --------------------------------------------------
    */
    public function getDataManager() {
        if ( ! $this->hasValidCriteria()) {
            return null;
        }
        /* Getting descriptor. */
        $descriptor = WidgetDescriptor::where('type', static::$histogramDescriptor)->first();
        if (is_null($descriptor)) {
            throw new DescriptorDoesNotExist("The descriptor for " . $histogramDescriptor . " does not exist", 1);
        }

        $managers = $this->user()->dataManagers()->where(
            'descriptor_id', $descriptor->id)->get();

        foreach ($managers as $generalManager) {
            $manager = $generalManager->getSpecific();
            if ($manager->getCriteria() == $this->getCriteria() && $manager instanceof HistogramDataManager) {
                /* Found a match. */
                return $manager;
            }
        }
        return null;
    }

    /**
     * getCurrentValue
     * Returning the current value.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getCurrentValue() {
        /* Getting manager. */
        $manager = $this->getDataManager();
        if (is_null($manager)) {
            return array();
        }
        $settings = $this->getSettings();
        return $manager->compare($settings['period'], $settings['multiplier']);
    }

    /**
     * handleAjax
     * Handling general ajax request.
     * --------------------------------------------------
     * @param array $postData
     * @return mixed
     * --------------------------------------------------
    */
    public function handleAjax($postData) {
    }
}
?>
