<?php

class GoogleAnalyticsTopSourcesWidget extends TableWidget implements iServiceWidget
{
    /* -- Settings -- */
    private static $rangeSettings = array(
        'range_start' => array(
            'name'       => 'Start range',
            'type'       => 'DATE',
            'validation' => 'required',
            'help_text'  => 'The start of the collection period. (YYYY-mm-dd)',
        ),
        'range_end' => array(
            'name'       => 'End range',
            'type'       => 'DATE',
            'validation' => 'required',
            'help_text'  => 'The end of the collection period. (YYYY-mm-dd)',
        ),
        'max_results' => array(
            'name'       => 'Number of sources.',
            'type'       => 'INT',
            'validation' => 'required',
            'help_text'  => 'The maximum number of sources you\'d like to see in your table.',
            'default'    => '5'
        ),
    );

    use GoogleAnalyticsWidgetTrait;

    /**
     * getSettingsFields
     * --------------------------------------------------
     * Returns the updated settings fields
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields() {
        /* Updating range settings in the static loader. */
        $rangeSettings = self::$rangeSettings;
        $rangeSettings['range_start']['default'] = Carbon::now()->subDays(30)->toDateString();
        $rangeSettings['range_end']['default'] = Carbon::now()->toDateString();

        return array_merge(parent::getSettingsFields(), self::$profileSettings, $rangeSettings);
    }

    /**
     * premiumUserCheck
     * Returns whether or not the resolution is a premium feature.
     * --------------------------------------------------
     * @return boolean
     * --------------------------------------------------
     */
     public function premiumUserCheck() {
        $passed = parent::premiumUserCheck();

        if ($passed === 0) {
            /* Further validation required. */
            $start = Carbon::createFromFormat('Y-m-d', $this->getSettings()['range_start']);
            if (Carbon::now()->diffInDays($start) > 31) {
                return -1;
            }
        }

        return $passed;
    }

    /**
     * updateData
     * Refreshing the widget data.
     * --------------------------------------------------
     * @param array options
     * @return string
     * --------------------------------------------------
    */
    public function updateData(array $options=array()) {
        if (empty($options)) {
            $this->dataManager()->collectData(array(
                'start'       => $this->getSettings()['range_start'],
                'end'         => $this->getSettings()['range_end'],
                'max_results' => $this->getSettings()['max_results'],
            ));
        } else {
            $this->dataManager()->collectData($options);
        }
    }

    /**
     * saveSettings
     * Collecting new data on change.
     * --------------------------------------------------
     * @param array $inputSettings
     * @param boolean $commit
     * --------------------------------------------------
    */
    public function saveSettings(array $inputSettings, $commit=TRUE) {
        $oldSettings = $this->getSettings();
        parent::saveSettings($inputSettings, $commit);
        if ($this->getSettings() != $oldSettings && $this->dataExists()) {
            $this->updateData();
        }
    }

}
?>
