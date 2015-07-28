<?php

class ReminderWidget extends Widget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'text' => array(
            'name'       => 'Reminder text',
            'type'       => 'TEXT',
            'validation' => 'required',
        ),
   );
    /* The settings to setup in the setup-wizard. */
    public static $setupSettings = array('text');

}

?>
