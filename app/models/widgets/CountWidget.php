<?php

abstract class CountWidget extends DataWidget implements iAjaxWidget
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
     * getTemplateMeta
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateMeta() {
        $meta = parent::getTemplateMeta();
        $meta['selectors']['count'] = 'count-' . $this->id;
        return $meta;
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
     * getData
     * Returning the current value.
     * --------------------------------------------------
     * @param array $postData
     * @return array
     * --------------------------------------------------
    */
    public function getData($postData=null) {
        /* Getting manager. */
        $settings = $this->getSettings();
        $this->data->setResolution($settings['period']);
        $this->data->setLength($settings['multiplier'] + 1);
        return array(
            'latest' => $this->data->getLatestValues(),
            'diff'   => $this->data->compare());
    }

    /**
     * assignData
     * Assigning the data to the widget.
     */
    public function assignData() {
        $descriptor = WidgetDescriptor::rememberForever()
            ->where('type', static::$histogramDescriptor)
            ->first();
        $this->data()->associate($descriptor->getDataObject($this));
    }

}
?>
