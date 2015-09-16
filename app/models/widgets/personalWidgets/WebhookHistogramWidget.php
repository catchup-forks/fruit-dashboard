<?php

class WebhookHistogramWidget extends MultipleHistogramWidget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'resolution' => array(
            'name'       => 'Resolution',
            'type'       => 'SCHOICE',
            'validation' => 'required',
            'default'    => 'daily',
            'help_text'  => 'The resolution of the chart.'
        ),
        'url' => array(
            'name'       => 'Webhook url',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The widget data will be populated from data on this url.'
        ),
        'name' => array(
            'name'       => 'Name',
            'type'       => 'TEXT',
            'validation' => 'required',
            'help_text'  => 'The name of the chart.'
        ),
   );

    /* The settings to setup in the setup-wizard. */
    public static $setupSettings = array('name', 'url');
    public static $criteriaSettings = array('url');

    /* -- Choice functions -- */
    public function resolution() {
        return array(
            'hourly'  => 'Hourly',
            'daily'   => 'Daily',
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            'yearly'  => 'Yearly'
        );
    }
}
?>
