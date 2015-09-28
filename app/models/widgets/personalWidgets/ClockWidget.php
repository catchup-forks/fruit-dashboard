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
            'analog'  => 'analogue'
        );
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
