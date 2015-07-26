<?php

class IframeWidget extends Widget
{
    /* -- Table specs -- */
    public static $type = 'iframe';

    /* -- Settings -- */
    public static $settingsFields = array(
        'url' => array(
            'name'       => 'Iframe URL',
            'type'       => 'TEXT',
            'validation' => 'required',
            'default'    => 'http://google.com'
        ),
   );
    // The settings to setup in the setup-wizard.
    public static $setupSettings = array('url');
}

?>
