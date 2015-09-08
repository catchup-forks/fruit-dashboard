<?php

class WebhookHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'frequency' => array(
            'name'       => 'Frequency',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily'
        ),
        'url' => array(
            'name'       => 'Webhook url',
            'type'       => 'TEXT',
            'validation' => 'required',
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
        ),
   );

    /* The settings to setup in the setup-wizard. */
    public static $setupSettings = array('name', 'url');
    public static $criteriaSettings = array('url');

}
?>
