<?php

class ServiceTableWidget extends TableWidget {
    /* -- Settings -- */
    private static $rangeSettings = array(
        'resolution' => array(
            'name'       => 'Time-scale',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'days',
            'help_text'  => 'Set the timescale for the periods.'
        ),
        'multiplier' => array(
            'name'       => 'Periods',
            'type'       => 'INT',
            'validation' => 'required',
            'default'    => '1',
            'help_text'  => 'The number of periods on the time scale.'
        ),
        'max_results' => array(
            'name'       => 'Number of sources.',
            'type'       => 'INT',
            'validation' => 'required',
            'help_text'  => 'The maximum number of sources you\'d like to see in your table.',
            'default'    => '5'
        ),
    );

    /* -- Choice functions -- */
    public function resolution() {
        return array(
            'days'   => 'Day',
            'weeks'  => 'Week',
            'months' => 'Month',
            'years'  => 'Year'
        );
    }

    /**
     * getSettingsFields
     * --------------------------------------------------
     * Returns the updated settings fields
     * @return array
     * --------------------------------------------------
     */
    public static function getSettingsFields() {
        /* Updating range settings in the static loader. */
        return array_merge(parent::getSettingsFields(), self::$rangeSettings);
    }

    /**
     * getTemplateData
     * Returning the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        $data = array(
            'header'  => array_keys($this->getHeader()),
            'content' => $this->getContent()
        );
        return array_merge(parent::getTemplateData(), array(
            'data'       => $data,
            'start_date' => $this->getStartDate()
        ));
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
        switch ($this->getSettings()['resolution']) {
            case 'days'  : $startDate = $now->subDays($multiplier); break;
            case 'weeks' : $startDate = $now->subWeeks($multiplier); break;
            case 'months': $startDate = $now->subMonths($multiplier); break;
            case 'years' : $startDate = $now->subYears($multiplier); break;
            default: $startDate = $now;
        }
        return $startDate->format('Y-m-d');
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
            $options = array(
                'start'       => $this->getStartDate(),
                'end'         => 'today',
                'max_results' => $this->getSettings()['max_results'],
            );
        }
        try {
            $this->data->collect($options);
        } catch (ServiceException $e) {
            Log::error('An error occurred during collecting data on #' . $this->data->id );
            $this->data->setState('data_source_error');
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
        $changedFields = parent::saveSettings($inputSettings, $commit);
        if ($oldSettings && $inputSettings &&
                $changedFields != FALSE &&
                $this->dataExists()) {
            $this->updateData();
        }
        return $changedFields;
    }

    /**
     * getData
     * Passing the job to the dataObject.
     */
    public function getData($postData=null)
    {
        $data = $this->data->decode();
        if (array_key_exists('header', $data)) {
            $data['header'] = array_keys($data['header']);
        }
        return $data;
    }
}
