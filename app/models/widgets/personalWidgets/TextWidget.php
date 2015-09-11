<?php

class TextWidget extends Widget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'text' => array(
            'name'       => 'Text',
            'type'       => 'TEXT',
            'validation' => 'required',
        ),
   );
    /* The settings to setup in the setup-wizard. */
    public static $setupSettings = array('text');

}

?>
