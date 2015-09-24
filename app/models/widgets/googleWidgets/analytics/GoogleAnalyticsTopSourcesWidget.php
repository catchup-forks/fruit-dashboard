<?php

class GoogleAnalyticsTopSourcesWidget extends CronWidget implements iServiceWidget
{
    /* -- Settings -- */
    private static $rangeSettings = array(
        'range_start' => array(
            'name'       => 'Start range',
            'type'       => 'DATE',
            'validation' => 'required',
            'help_text'  => 'The start of the collection period. (YYYY-mm-dd)',
            'default'    => '2015-01-01'
        ),
        'range_end' => array(
            'name'       => 'End range',
            'type'       => 'DATE',
            'validation' => 'required',
            'help_text'  => 'The end of the collection period. (YYYY-mm-dd)',
            'default'    => '2015-12-31'
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
        return array_merge(parent::getSettingsFields(), self::$propertySettings, self::$rangeSettings);
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
        $this->dataManager()->collectData(array(
            'start'       => $this->getSettings()['range_start'],
            'end'         => $this->getSettings()['range_end'],
            'max_results' => $this->getSettings()['max_results'],
        ));

        /* Faling back to active. */
        $this->state = 'active';
        $this->save();
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
            if (Carbon::now()->diffInDays($start) > 30) {
                return -1;
            }
        }

        return $passed;
    }
}
?>
