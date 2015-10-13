<?php

abstract class CountWidget extends Widget implements iAjaxWidget
{
    use NumericWidgetTrait;
    use DefaultAjaxWidgetTrait;
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
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        $values = $this->getData();
        return array_merge(parent::getTemplateData(), array(
            'valueDiff'    => $values['diff'],
            'valueCurrent' => $values['latest'],
            'format'       => $this->getFormat()
        ));
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
    protected function getDataManager() {
        /* Getting descriptor. */
        $descriptor = WidgetDescriptor::where('type', static::$histogramDescriptor)->first();

        if (is_null($descriptor)) {
            throw new DescriptorDoesNotExist("The descriptor for " . static::$histogramDescriptor . " does not exist", 1);
        }

        /* Calling the DM retriever on the specific descriptor. */
        return $descriptor->getDataManager($this);
    }

    /**
     * getData
     * Returning the current value.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getData() {
        /* Getting manager. */
        $manager = $this->getDataManager();
        if (is_null($manager)) {
            return array();
        }
        $settings = $this->getSettings();
        $manager->setResolution($settings['period']);
        $manager->setLength($settings['multiplier'] + 1);
        return array(
            'latest' => $manager->getLatestValues(),
            'diff'   => $manager->compare());
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

    /**
     * save
     * Looking for managers.
     * --------------------------------------------------
     * @param array $options
     * @return null
     * --------------------------------------------------
    */
    public function save(array $options=array()) {
        parent::save($options);
        $dataManager = $this->getDataManager();
        if ( ! is_null($dataManager)) {
            $this->data()->associate($dataManager->data);
            parent::save($options);
        }

        return TRUE;
    }
}
?>
