<?php

class TwitterMentionsWidget extends DataWidget implements iServiceWidget
{
    use TwitterWidgetTrait;
    /* -- Settings -- */
    private static $rangeSettings = array(
        'count' => array(
            'name'       => 'Number of mentions.',
            'type'       => 'INT',
            'validation' => 'required|max:10',
            'help_text'  => 'The maximum number of mentinos you\'d like to see in your widget (maximum:10).',
            'default'    => '5'
        ),
    );

    /**
     * getTemplateData
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'data' => $this->getData(),
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
        return array_merge(parent::getSettingsFields(), self::$rangeSettings);
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
                'count' => $this->getSettings()['count']
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
    public function saveSettings(array $inputSettings, $commit=true) {
        $oldSettings = $this->getSettings();
        $changedFields = parent::saveSettings($inputSettings, $commit);
        if ($changedFields != false && $this->dataExists()) {
            $this->updateData();
        }
        return $changedFields;
    }
}
?>
