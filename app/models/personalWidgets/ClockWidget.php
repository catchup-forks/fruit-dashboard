<?php

class ClockWidget extends Widget
{
    /* -- Table specs -- */
    public static $type = 'clock';

    /* -- Settings -- */
    public static $settingsFields = array(
        'clock_type' => array(
            'name' => 'Type',
            'type' => 'SCHOICE',
            'validation' => 'required',
        ),
   );
    // The settings to setup in the setup-wizard.
    public static $setupSettings = array();

    /* Choices functions */
    public function clock_type() {
        return array(
            'digital' => 'digital',
            'analog'  => 'analog'
        );
    }
}

?>
