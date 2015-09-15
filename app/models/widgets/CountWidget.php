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
        if (isset($postData['state_query']) && $postData['state_query']) {
            /* Get state query signal */
            if ($this->state == 'loading') {
                return array('ready' => FALSE);
            } else if($this->state == 'active') {
                return array(
                    'ready' => TRUE,
                    'data'  => $this->getCurrentValue($postData)
                );
            } else {
                return array('ready' => FALSE);
            }
        }
        if (isset($postData['refresh_data']) && $postData['refresh_data']) {
            /* Refresh signal */
            $this->refreshWidget();
        }
    }

    /**
     * Refreshing the widget data.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
    */
    public function refreshWidget() {
        $this->state = 'loading';
        $this->save();

        /* Refreshing widget data. */
        $this->getDataManager()->collectData();

        /* Faling back to active. */
        $this->state = 'active';
        $this->save();
    }
}
?>
