<?php

class TimerWidget extends Widget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'countdown' => array(
            'name'       => 'Time',
            'type'       => 'INT',
            'validation' => 'required'
        ),
   );
    /* The settings to setup in the setup-wizard. */
    public static $setupSettings = array('countdown');

}

?>
