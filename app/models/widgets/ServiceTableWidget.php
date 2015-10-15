<?php

class ServiceTableWidget extends TableWidget {
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

        return array_merge(parent::getSettingsFields(), $rangeSettings);
    }
}