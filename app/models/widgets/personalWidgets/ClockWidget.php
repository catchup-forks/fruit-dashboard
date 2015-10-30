<?php

class ClockWidget extends Widget
{
    /* -- Settings -- */
    private static $clockSettings = array(
        'clock_type' => array(
            'name'       => 'Type',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'digital'
        ),
   );

    /* Choices functions */
    public function clock_type() {
        return array(
            'digital' => 'digital',
        );
    }

    /**
     * getTemplateData
     * Return the mostly used values in the template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function getTemplateData() {
        return array_merge(parent::getTemplateData(), array(
            'currentTime' => Carbon::now()->format('H:i')
        ));
    }

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$clockSettings);
     }
}

?>
