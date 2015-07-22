<?php

class ClockWidget extends Widget
{
    /* -- Table specs -- */
    public static $type = 'clock';

    /* -- Settings -- */
    public static $settingsFields = array(
        'am_pm' => array('name' => 'AM/pm', 'type' => 'SCHOICE', 'validation'),
        'f1' => array('name' => 'Field#1', 'type' => 'TEXT'),
        'f2' => array('name' => 'Field#3', 'type' => 'INT'),
        'f3' => array('name' => 'Field#3', 'type' => 'DATE'),
        'f4' => array('name' => 'Field#4', 'type' => 'FLOAT'),
        'f5' => array('name' => 'Field#5', 'type' => 'TEXT'),
   );
    // The settings to setup in the setup-wizard.
    public static $setupSettings = array('am_pm');

    /* Choices functions */
    public function am_pm() {
        return array(
            'am' => 'AM',
            'pm' => 'PM'
        );
    }
}

?>
