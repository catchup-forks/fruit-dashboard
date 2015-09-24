<?php

class TwitterMentionsWidget extends CronWidget implements iServiceWidget
{
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
    use TwitterWidgetTrait;

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
            'count' => $this->getSettings()['count'],
        ));

        /* Faling back to active. */
        $this->state = 'active';
        $this->save();
    }
}
?>
