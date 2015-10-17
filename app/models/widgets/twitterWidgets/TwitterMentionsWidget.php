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
     * Returning the mostly used values in the template.
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
            $this->data->collect(array('count' => $this->getSettings()['count']));
        } else {
            $this->data->collect($options);
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
