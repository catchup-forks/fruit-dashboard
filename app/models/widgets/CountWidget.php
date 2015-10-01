<?php

abstract class CountWidget extends Widget implements iAjaxWidget
{
    use NumericWidgetTrait;
    protected static $histogramDescriptor = '';

    /* -- Settings -- */
    protected static $countWidgetSettings = array(
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
        );
    }

    /**
     * checkIntegrity
     * Adding manager loading integrity check.
    */
    public function checkIntegrity() {
        parent::checkIntegrity();
        if ($this->getDataManager()->data->raw_value == 'loading') {
            $this->setState('loading');
        } else if ($this->state != 'setup_required') {
            $this->setState('active');
        }
    }

    /**
     * getSettingsFields
     * --------------------------------------------------
     * Returns the updated settings fields
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$countWidgetSettings);
    }

    /**
     * getStartDate
     * --------------------------------------------------
     * Returns the start date based on settings.
     * @return array
     * --------------------------------------------------
     */
    public function getStartDate() {
        $multiplier = $this->getSettings()['multiplier'];
        $now = Carbon::now();
        switch ($this->getSettings()['period']) {
            case 'hours' : return $now->subHours($multiplier)->format('H:i');
            case 'days'  : return $now->subDays($multiplier)->format('l (m.d)');
            case 'weeks' : return $now->subWeeks($multiplier)->format('Y.m.d');
            case 'months': return $now->subMonths($multiplier)->format('F, Y');
            default: return '';
        }
    }

    /**
     * getDataManager
     * Returning the corresponding DataManager
     * --------------------------------------------------
     * @return DataManager
     * --------------------------------------------------
    */
    public function getDataManager() {
        /* Getting descriptor. */
        $descriptor = WidgetDescriptor::where('type', static::$histogramDescriptor)->first();
        if (is_null($descriptor)) {
            throw new DescriptorDoesNotExist("The descriptor for " . static::$histogramDescriptor . " does not exist", 1);
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

        /* No manager found. */
        if ($this->hasValidCriteria()) {
            return DataManager::createManager(
                $this->user(),
                $descriptor,
                $this->getCriteria()
            );
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
        return array('latest' => $manager->getLatestValues(), 'diff' => $manager->compare($settings['period'], $settings['multiplier']));
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
            try {
                $this->refreshWidget();
            } catch (ServiceException $e) {
                Log::error($e->getMessage());
                return array('status'  => FALSE,
                             'message' => 'We couldn\'t refresh your data, because the service is unavailable.');
            }
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
