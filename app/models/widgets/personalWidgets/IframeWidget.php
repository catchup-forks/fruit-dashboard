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
        'div_id' => array(
            'name'       => 'Specific div id',
            'type'       => 'TEXT'
        ),
        'pointer_events' => array(
            'name'       => 'Pointer events',
            'type'       => 'BOOL',
            'default'    => FALSE,
        ),
   );
    // The settings to setup in the setup-wizard.
    public static $setupSettings = array('url');
}

?>
